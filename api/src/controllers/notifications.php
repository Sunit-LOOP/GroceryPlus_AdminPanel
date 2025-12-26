<?php
// api/src/controllers/notifications.php

class NotificationController {
    public static function handleNotifications($method, $id, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();
        if (!$user) {
            sendError('Invalid token', 401);
        }

        switch ($method) {
            case 'GET':
                if ($id) {
                    self::getNotification($pdo, $id, $user['id']);
                } else {
                    self::getNotifications($pdo, $user['id']);
                }
                break;
            case 'PUT':
                if (!$id) {
                    sendError('Notification ID required', 400);
                }
                self::markAsRead($pdo, $id, $user['id']);
                break;
            case 'DELETE':
                if (!$id) {
                    sendError('Notification ID required', 400);
                }
                self::deleteNotification($pdo, $id, $user['id']);
                break;
            default:
                sendError('Method not allowed', 405);
        }
    }

    private static function getNotifications($pdo, $userId) {
        try {
            $stmt = $pdo->prepare("
                SELECT notification_id, title, message, is_read, created_at
                FROM notifications
                WHERE user_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(['notifications' => $notifications]);
        } catch (PDOException $e) {
            error_log("Get notifications error: " . $e->getMessage());
            sendError('Failed to retrieve notifications', 500);
        }
    }

    private static function getNotification($pdo, $notificationId, $userId) {
        try {
            $stmt = $pdo->prepare("
                SELECT notification_id, title, message, is_read, created_at
                FROM notifications
                WHERE notification_id = ? AND user_id = ?
            ");
            $stmt->execute([$notificationId, $userId]);
            $notification = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$notification) {
                sendError('Notification not found', 404);
            }

            sendResponse(['notification' => $notification]);
        } catch (PDOException $e) {
            error_log("Get notification error: " . $e->getMessage());
            sendError('Failed to retrieve notification', 500);
        }
    }

    private static function markAsRead($pdo, $notificationId, $userId) {
        try {
            $stmt = $pdo->prepare("
                UPDATE notifications
                SET is_read = 1
                WHERE notification_id = ? AND user_id = ?
            ");
            $stmt->execute([$notificationId, $userId]);

            if ($stmt->rowCount() > 0) {
                sendResponse(['success' => true, 'message' => 'Notification marked as read']);
            } else {
                sendError('Notification not found or already read', 404);
            }
        } catch (PDOException $e) {
            error_log("Mark as read error: " . $e->getMessage());
            sendError('Failed to mark notification as read', 500);
        }
    }

    private static function deleteNotification($pdo, $notificationId, $userId) {
        try {
            $stmt = $pdo->prepare("
                DELETE FROM notifications
                WHERE notification_id = ? AND user_id = ?
            ");
            $stmt->execute([$notificationId, $userId]);

            if ($stmt->rowCount() > 0) {
                sendResponse(['success' => true, 'message' => 'Notification deleted']);
            } else {
                sendError('Notification not found', 404);
            }
        } catch (PDOException $e) {
            error_log("Delete notification error: " . $e->getMessage());
            sendError('Failed to delete notification', 500);
        }
    }
}
