<?php
// api/src/controllers/users.php

class UserController {
    public static function handleUsers($method, $id, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();
        // Admin access check for all methods except GET (for own profile)
        if ($user['type'] !== 'admin') {
            if ($method === 'GET' && (int)$id === (int)$user['id']) {
                // Allow user to get their own profile
            } else {
                sendError('Admin access required', 403);
            }
        }

        switch ($method) {
            case 'GET':
                if ($id) {
                    $stmt = $pdo->prepare("SELECT user_id, user_name, user_email, user_phone, user_type, created_at FROM users WHERE user_id = ?");
                    $stmt->execute([(int)$id]);
                    $requestedUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($requestedUser) {
                        sendResponse($requestedUser);
                    } else {
                        sendError('User not found', 404);
                    }
                } else {
                    // Only admin can get all users
                    if ($user['type'] !== 'admin') {
                        sendError('Admin access required', 403);
                    }
                    $stmt = $pdo->query("SELECT user_id, user_name, user_email, user_phone, user_type, created_at FROM users ORDER BY created_at DESC");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;

            case 'PUT':
                if (!$id) sendError('User ID required', 400);
                // Admin only for PUT on other users, user can update own profile.
                if ($user['type'] !== 'admin' && (int)$id !== (int)$user['id']) {
                        sendError('Admin access required to update other users', 403);
                }

                $data = getJsonInput();
                $updates = [];
                $params = [];
                $allowedFields = ['user_name', 'user_email', 'user_phone'];
                if ($user['type'] === 'admin') { // Admin can change user_type as well
                    $allowedFields[] = 'user_type';
                }

                foreach ($allowedFields as $field) {
                    if (isset($data[$field])) {
                        // Basic validation for email/phone if they are updated
                        if ($field === 'user_email' && !validateEmail($data[$field])) {
                            sendError('Invalid email format', 422);
                        }
                        if ($field === 'user_phone' && !validatePhone($data[$field])) {
                            sendError('Invalid phone format', 422);
                        }
                        $updates[] = "$field = ?";
                        $params[] = sanitizeString($data[$field]);
                    }
                }
                
                if (empty($updates)) {
                    sendError('No fields to update', 400);
                }

                $params[] = (int)$id;
                
                try {
                    $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?");
                    $stmt->execute($params);

                    if ($stmt->rowCount() > 0) {
                        sendResponse(['success' => true, 'message' => 'User updated successfully']);
                    } else {
                        sendError('User not found or no changes made', 404);
                    }
                } catch (PDOException $e) {
                    error_log("User update error: " . $e->getMessage());
                    sendError('Failed to update user', 500);
                }
                break;

            case 'DELETE':
                if (!$id) sendError('User ID required', 400);
                // Only admin can delete users, and an admin cannot delete themselves
                if ($user['type'] !== 'admin' || (int)$id === (int)$user['id']) {
                        sendError('Admin access required and cannot delete own account', 403);
                }

                try {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                    $stmt->execute([(int)$id]);

                    if ($stmt->rowCount() > 0) {
                        sendResponse(['success' => true, 'message' => 'User deleted successfully']);
                    } else {
                        sendError('User not found', 404);
                    }
                } catch (PDOException $e) {
                    error_log("User deletion error: " . $e->getMessage());
                    sendError('Failed to delete user', 500);
                }
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
