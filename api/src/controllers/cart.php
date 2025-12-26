<?php

class CartController {
    public static function handleCart($method, $id, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();

        switch ($method) {
            case 'GET':
                $stmt = $pdo->prepare("
                    SELECT c.*, p.product_name, p.price, p.image
                    FROM cart_items c
                    LEFT JOIN products p ON c.product_id = p.product_id
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$user['id']]);
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Add image URLs
                foreach ($cartItems as &$item) {
                    $item['image_url'] = $item['image'] ? "http://localhost/groceryplus/images/{$item['image']}" : null;
                }

                sendResponse(['cart' => $cartItems]);
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);

                if (!isset($data['product_id'], $data['quantity'])) {
                    sendError('Product ID and quantity required');
                }

                // Check if item already in cart
                $stmt = $pdo->prepare("SELECT cart_id FROM cart_items WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$user['id'], $data['product_id']]);
                $existing = $stmt->fetch();

                if ($existing) {
                    // Update quantity
                    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cart_id = ?");
                    $stmt->execute([$data['quantity'], $existing['cart_id']]);
                } else {
                    // Add new item
                    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$user['id'], $data['product_id'], $data['quantity']]);
                }

                sendResponse(['success' => true], 201);
                break;

            case 'PUT':
                if (!$id) sendError('Cart item ID required');

                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND user_id = ?");
                $stmt->execute([$data['quantity'], $id, $user['id']]);

                sendResponse(['success' => true]);
                break;

            case 'DELETE':
                if (!$id) sendError('Cart item ID required');

                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?");
                $stmt->execute([$id, $user['id']]);

                sendResponse(['success' => true]);
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
?>