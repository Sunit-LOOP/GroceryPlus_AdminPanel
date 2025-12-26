<?php

class ReviewController {
    public static function handleReviews($method, $id, $pdo) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    // Get reviews for specific product
                    $stmt = $pdo->prepare("
                        SELECT r.*, u.user_name
                        FROM reviews r
                        LEFT JOIN users u ON r.user_id = u.user_id
                        WHERE r.product_id = ?
                        ORDER BY r.created_at DESC
                    ");
                    $stmt->execute([$id]);
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                } else {
                    // Get all reviews
                    $stmt = $pdo->query("
                        SELECT r.*, u.user_name, p.product_name
                        FROM reviews r
                        LEFT JOIN users u ON r.user_id = u.user_id
                        LEFT JOIN products p ON r.product_id = p.product_id
                        ORDER BY r.created_at DESC
                    ");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;

            case 'POST':
                if (!validateToken()) sendError('Unauthorized', 401);

                $user = getUserFromToken();
                $data = json_decode(file_get_contents('php://input'), true);

                if (!isset($data['product_id'], $data['rating'], $data['review'])) {
                    sendError('Product ID, rating, and review required');
                }

                $stmt = $pdo->prepare("
                    INSERT INTO reviews (user_id, product_id, rating, review_text)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$user['id'], $data['product_id'], $data['rating'], $data['review']]);

                sendResponse(['success' => true], 201);
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
?>