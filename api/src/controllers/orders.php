<?php

class OrderController {
    public static function handleOrders($method, $id, $pdo) {
        if (!validateToken()) {
            sendError('Unauthorized', 401);
        }

        $user = getUserFromToken();

        switch ($method) {
            case 'GET':
                if ($id) {
                    $stmt = $pdo->prepare("
                        SELECT o.*, u.user_name, u.user_email
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.user_id
                        WHERE o.order_id = ?
                    ");
                    $stmt->execute([(int)$id]);
                    $order = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($order) {
                        // Get order items
                        $stmt = $pdo->prepare("
                            SELECT oi.*, p.product_name, p.image
                            FROM order_items oi
                            LEFT JOIN products p ON oi.product_id = p.product_id
                            WHERE oi.order_id = ?
                        ");
                        $stmt->execute([(int)$id]);
                        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Add image URLs
                        foreach ($order['items'] as &$item) {
                            $item['image_url'] = $item['image'] ? API_BASE_URL . "/images/{$item['image']}" : null;
                        }

                        sendResponse($order);
                    } else {
                        sendError('Order not found', 404);
                    }
                } else {
                    // List orders for current user or all for admin
                    $query = "
                        SELECT o.*, u.user_name, u.user_email,
                               COUNT(oi.order_item_id) as item_count
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.user_id
                        LEFT JOIN order_items oi ON o.order_id = oi.order_id
                    ";

                    if ($user['type'] === 'user') {
                        $query .= " WHERE o.user_id = ? ";
                        $stmt = $pdo->prepare($query . " GROUP BY o.order_id ORDER BY o.created_at DESC");
                        $stmt->execute([(int)$user['id']]);
                    } else {
                        $query .= " GROUP BY o.order_id ORDER BY o.created_at DESC";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute();
                    }

                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;

            case 'POST':
                $data = getJsonInput();

                if (!isset($data['user_id'], $data['items'])) {
                    sendError('User ID and items required', 400);
                }
                // Ensure user ID matches token if not admin
                if ($user['type'] !== 'admin' && (int)$data['user_id'] !== (int)$user['id']) {
                    sendError('Cannot create order for another user', 403);
                }

                // Calculate total
                $total = 0;
                foreach ($data['items'] as $item) {
                    if (!isset($item['product_id'], $item['quantity'], $item['price']) || $item['quantity'] <= 0 || $item['price'] < 0) {
                        sendError('Invalid item data in order', 400);
                    }
                    $total += $item['price'] * $item['quantity'];
                }

                try {
                    $pdo->beginTransaction();

                    // Create order
                    $stmt = $pdo->prepare("
                        INSERT INTO orders (user_id, total_amount, delivery_fee, status, order_date)
                        VALUES (?, ?, ?, 'pending', datetime('now'))
                    ");
                    $stmt->execute([(int)$data['user_id'], $total, (float)($data['delivery_fee'] ?? 0)]);
                    $orderId = (int)$pdo->lastInsertId();

                    // Add items
                    foreach ($data['items'] as $item) {
                        $stmt = $pdo->prepare("
                            INSERT INTO order_items (order_id, product_id, quantity, price)
                            VALUES (?, ?, ?, ?)
                        ");
                        $stmt->execute([$orderId, (int)$item['product_id'], (int)$item['quantity'], (float)$item['price']]);
                    }

                    $pdo->commit();
                    sendResponse(['order_id' => $orderId, 'message' => 'Order created successfully'], 201);
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    error_log("Order creation error: " . $e->getMessage());
                    sendError('Failed to create order', 500);
                }
                break;

            case 'PUT':
                if (!$id) sendError('Order ID required', 400);

                $data = getJsonInput();

                // Fetch the order to check ownership
                $stmt = $pdo->prepare("SELECT user_id FROM orders WHERE order_id = ?");
                $stmt->execute([(int)$id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$order) {
                    sendError('Order not found', 404);
                }

                // If the user is not an admin, they can only cancel their own orders.
                if ($user['type'] !== 'admin') {
                    if ((int)$order['user_id'] !== (int)$user['id']) {
                        sendError('Cannot update orders for another user', 403);
                    }
                    if (!isset($data['status']) || $data['status'] !== 'cancelled') {
                        sendError('You can only cancel your own order', 403);
                    }
                }


                try {
                    $updates = [];
                    $params = [];
                    $shippedDate = null;
                    if (isset($data['status'])) {
                        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                        if (!in_array($data['status'], $allowedStatuses)) {
                            sendError('Invalid order status', 422);
                        }
                        $updates[] = "status = ?";
                        $params[] = sanitizeString($data['status']);
                        if ($data['status'] === 'shipped' || $data['status'] === 'delivered') {
                            $updates[] = "shipped_date = ?";
                            $params[] = date('Y-m-d H:i:s');
                        }
                    }
                    // Add other updatable fields here if necessary (e.g., delivery_fee by admin)

                    if (empty($updates)) {
                        sendError('No fields to update', 400);
                    }

                    $params[] = (int)$id;

                    $stmt = $pdo->prepare("UPDATE orders SET " . implode(', ', $updates) . " WHERE order_id = ?");
                    $stmt->execute($params);

                    if ($stmt->rowCount() > 0) {
                        sendResponse(['success' => true, 'message' => 'Order updated successfully']);
                    } else {
                        sendError('Order not found or no changes made', 404);
                    }
                } catch (PDOException $e) {
                    error_log("Order update error: " . $e->getMessage());
                    sendError('Failed to update order', 500);
                }
                break;

            case 'DELETE':
                if (!$id) sendError('Order ID required', 400);
                // Only admin can delete orders
                if ($user['type'] !== 'admin') {
                    sendError('Admin access required', 403);
                }

                try {
                    $pdo->beginTransaction();

                    // Delete order items first
                    $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
                    $stmt->execute([(int)$id]);

                    // Then delete the order
                    $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
                    $stmt->execute([(int)$id]);

                    $pdo->commit();
                    if ($stmt->rowCount() > 0) {
                        sendResponse(['success' => true, 'message' => 'Order deleted successfully']);
                    } else {
                        sendError('Order not found', 404);
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    error_log("Order deletion error: " . $e->getMessage());
                    sendError('Failed to delete order', 500);
                }
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
?>