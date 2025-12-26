<?php

class FavoritesController {
    public static function handleFavorites($method, $id, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();

        switch ($method) {
            case 'GET':
                $stmt = $pdo->prepare("
                    SELECT f.favorite_id, p.product_id, p.product_name, p.price, p.image
                    FROM favorites f
                    LEFT JOIN products p ON f.product_id = p.product_id
                    WHERE f.user_id = ?
                ");
                $stmt->execute([$user['id']]);
                $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Add image URLs
                foreach ($favorites as &$fav) {
                    $fav['image_url'] = $fav['image'] ? "http://localhost/groceryplus/images/{$fav['image']}" : null;
                }

                sendResponse(['favorites' => $favorites]);
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);

                if (!isset($data['product_id'])) {
                    sendError('Product ID required');
                }

                $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
                $stmt->execute([$user['id'], $data['product_id']]);

                sendResponse(['success' => true], 201);
                break;

            case 'DELETE':
                if (!$id) sendError('Favorite ID required');

                $stmt = $pdo->prepare("DELETE FROM favorites WHERE favorite_id = ? AND user_id = ?");
                $stmt->execute([$id, $user['id']]);

                sendResponse(['success' => true]);
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
?>