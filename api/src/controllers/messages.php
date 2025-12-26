<?php

class MessageController {
    public static function handleMessages($method, $id, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();

        switch ($method) {
            case 'GET':
                if ($id) {
                    // Get conversation with specific user
                    $stmt = $pdo->prepare("
                        SELECT m.*, u.user_name as sender_name
                        FROM messages m
                        LEFT JOIN users u ON m.sender_id = u.user_id
                        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
                        ORDER BY m.created_at ASC
                    ");
                    $stmt->execute([$user['id'], $id, $id, $user['id']]);
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    // Get all conversations for user
                    $stmt = $pdo->prepare("
                        SELECT DISTINCT
                            CASE
                                WHEN m.sender_id = ? THEN m.receiver_id
                                ELSE m.sender_id
                            END as other_user_id,
                            u.user_name,
                            m.message_text,
                            m.created_at,
                            m.is_read
                        FROM messages m
                        LEFT JOIN users u ON (
                            CASE
                                WHEN m.sender_id = ? THEN m.receiver_id
                                ELSE m.sender_id
                            END = u.user_id
                        )
                        WHERE m.sender_id = ? OR m.receiver_id = ?
                        ORDER BY m.created_at DESC
                    ");
                    $stmt->execute([$user['id'], $user['id'], $user['id'], $user['id']]);

                    $conversations = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $conversations[$row['other_user_id']] = $row;
                    }

                    sendResponse(['conversations' => array_values($conversations)]);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);

                if (!isset($data['receiver_id'], $data['message'])) {
                    sendError('Receiver ID and message required');
                }

                $stmt = $pdo->prepare("
                    INSERT INTO messages (sender_id, receiver_id, message_text, is_read)
                    VALUES (?, ?, ?, 0)
                ");
                $stmt->execute([$user['id'], $data['receiver_id'], $data['message']]);

                sendResponse(['success' => true], 201);
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
?>