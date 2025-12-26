<?php
// api/src/controllers/likes.php

class LikesController {
    public static function handleLikes($method, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();
        if (!$user || $user['type'] !== 'user') {
            sendError('User access required', 403);
        }

        switch ($method) {
            case 'POST':
                self::addLike($pdo, $user['id']);
                break;
            case 'DELETE':
                // DELETE can be for a specific like ID or by entity
                // For simplicity, we'll allow DELETE by entity type and ID in the request body
                self::removeLike($pdo, $user['id']);
                break;
            case 'GET':
                 self::getLikes($pdo, $user['id']);
                 break;
            default:
                sendError('Method not allowed', 405);
        }
    }

    private static function addLike($pdo, $userId) {
        $data = getJsonInput();

        if (!isset($data['entity_type'], $data['entity_id'])) {
            sendError('Entity type and entity ID required', 400);
        }

        $entityType = sanitizeString($data['entity_type']);
        $entityId = (int)$data['entity_id'];

        // Optional: Add validation here to ensure entity_type is allowed
        $allowedEntityTypes = ['product', 'review', 'message']; // Define allowed types
        if (!in_array($entityType, $allowedEntityTypes)) {
            sendError('Invalid entity type', 400);
        }
        
        // Check if the entity exists
        if (!self::entityExists($pdo, $entityType, $entityId)) {
            sendError('Entity not found or invalid', 404);
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO likes (user_id, entity_type, entity_id) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $entityType, $entityId]);
            sendResponse(['success' => true, 'message' => 'Entity liked successfully'], 201);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // SQLite constraint violation (unique constraint)
                sendError('You have already liked this entity', 409);
            }
            error_log("Like add error: " . $e->getMessage());
            sendError('Failed to like entity', 500);
        }
    }

    private static function removeLike($pdo, $userId) {
        $data = getJsonInput();

        if (!isset($data['entity_type'], $data['entity_id'])) {
            sendError('Entity type and entity ID required for unliking', 400);
        }

        $entityType = sanitizeString($data['entity_type']);
        $entityId = (int)$data['entity_id'];

        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND entity_type = ? AND entity_id = ?");
        $stmt->execute([$userId, $entityType, $entityId]);

        if ($stmt->rowCount() > 0) {
            sendResponse(['success' => true, 'message' => 'Entity unliked successfully']);
        } else {
            sendError('Like not found or you do not have permission to unlike this entity', 404);
        }
    }
    
    private static function getLikes($pdo, $userId) {
        $entityType = $_GET['entity_type'] ?? null;
        $entityId = $_GET['entity_id'] ?? null;

        $sql = "SELECT * FROM likes WHERE user_id = ?";
        $params = [$userId];

        if ($entityType) {
            $sql .= " AND entity_type = ?";
            $params[] = sanitizeString($entityType);
        }
        if ($entityId) {
            $sql .= " AND entity_id = ?";
            $params[] = (int)$entityId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(['likes' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    // Helper to check if an entity exists before liking it
    private static function entityExists($pdo, $entityType, $entityId) {
        $tableName = '';
        $idColumn = '';

        switch ($entityType) {
            case 'product':
                $tableName = 'products';
                $idColumn = 'product_id';
                break;
            case 'review':
                $tableName = 'reviews';
                $idColumn = 'review_id';
                break;
            case 'message':
                $tableName = 'messages';
                $idColumn = 'message_id';
                break;
            default:
                return false; // Unknown entity type
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$tableName} WHERE {$idColumn} = ?");
        $stmt->execute([$entityId]);
        return $stmt->fetchColumn() > 0;
    }
}
