<?php
// api/src/controllers/products.php

class ProductController {
    public static function handleProducts($method, $id, $pdo) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    // Get single product
                    $stmt = $pdo->prepare("
                        SELECT p.*, c.category_name, v.vendor_name
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.category_id
                        LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                        WHERE p.product_id = ?
                    ");
                    $stmt->execute([(int)$id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($product) {
                        $product['image_url'] = $product['image'] ? API_BASE_URL . "/images/{$product['image']}" : null;
                        sendResponse($product);
                    } else {
                        sendError('Product not found', 404);
                    }
                } else {
                    // Get all products with filters
                    $category = isset($_GET['category']) ? sanitizeString($_GET['category']) : null;
                    $search = isset($_GET['search']) ? sanitizeString($_GET['search']) : null;
                    $limit = (int)($_GET['limit'] ?? 50);
                    $offset = (int)($_GET['offset'] ?? 0);

                    // Validate pagination
                    $limit = min($limit, 100); // Max 100 items per request
                    $limit = max($limit, 1);
                    $offset = max($offset, 0);

                    $query = "
                        SELECT p.*, c.category_name, v.vendor_name,
                               COUNT(r.review_id) as review_count,
                               AVG(r.rating) as average_rating
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.category_id
                        LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                        LEFT JOIN reviews r ON p.product_id = r.product_id
                        WHERE 1=1
                    ";
                    $params = [];

                    if ($category) {
                        $query .= " AND c.category_name LIKE ?";
                        $params[] = "%$category%";
                    }

                    if ($search) {
                        $query .= " AND (p.product_name LIKE ? OR p.description LIKE ?)";
                        $params[] = "%$search%";
                        $params[] = "%$search%";
                    }

                    $query .= " GROUP BY p.product_id ORDER BY p.product_id DESC LIMIT ? OFFSET ?";
                    $params[] = $limit;
                    $params[] = $offset;

                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Add image URLs and format ratings
                    foreach ($products as &$product) {
                        $product['image_url'] = $product['image'] ? API_BASE_URL . "/images/{$product['image']}" : null;
                        $product['average_rating'] = $product['average_rating'] ? round($product['average_rating'], 2) : 0;
                        $product['review_count'] = (int)$product['review_count'];
                    }

                    sendResponse([
                        'products' => $products,
                        'count' => count($products),
                        'limit' => $limit,
                        'offset' => $offset
                    ]);
                }
                break;

            case 'POST':
                if (!validateToken()) {
                    sendError('Unauthorized', 401);
                }

                $user = getUserFromToken();
                if (!$user || $user['type'] !== 'admin') {
                    sendError('Admin access required', 403);
                }

                $data = getJsonInput();
                $errors = [];

                // Validate product data
                if (empty($data['product_name'])) {
                    $errors['product_name'] = 'Product name is required';
                }

                if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
                    $errors['price'] = 'Valid price is required';
                }

                if (isset($data['stock_quantity']) && (!is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0)) {
                    $errors['stock_quantity'] = 'Stock quantity must be a positive number';
                }

                if (!empty($errors)) {
                    sendError('Validation failed', 422, $errors);
                }

                $stmt = $pdo->prepare("
                    INSERT INTO products (product_name, category_id, price, description, stock_quantity, vendor_id, image)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                try {
                    $stmt->execute([
                        sanitizeString($data['product_name']),
                        (int)($data['category_id'] ?? 1),
                        (float)$data['price'],
                        sanitizeString($data['description'] ?? ''),
                        (int)($data['stock_quantity'] ?? 0),
                        (int)($data['vendor_id'] ?? 1),
                        isset($data['image']) ? sanitizeString($data['image']) : null
                    ]);

                    sendResponse([
                        'product_id' => (int)$pdo->lastInsertId(),
                        'message' => 'Product created successfully'
                    ], 201);
                } catch (PDOException $e) {
                    error_log("Product creation error: " . $e->getMessage());
                    sendError('Failed to create product', 500);
                }
                break;

            case 'PUT':
                if (!validateToken() || !$id) {
                    sendError('Unauthorized or invalid request', 401);
                }

                $user = getUserFromToken();
                if (!$user || $user['type'] !== 'admin') {
                    sendError('Admin access required', 403);
                }

                $data = getJsonInput();
                $updates = [];
                $params = [];
                $allowedFields = ['product_name', 'category_id', 'price', 'description', 'stock_quantity', 'vendor_id', 'image'];

                foreach ($allowedFields as $field) {
                    if (isset($data[$field])) {
                        // Validate specific fields
                        if ($field === 'price' && (empty($data[$field]) || !is_numeric($data[$field]) || $data[$field] < 0)) {
                            sendError('Invalid price value', 422);
                        }
                        if ($field === 'stock_quantity' && (isset($data[$field]) && (!is_numeric($data[$field]) || $data[$field] < 0))) {
                            sendError('Invalid stock quantity', 422);
                        }

                        $updates[] = "$field = ?";
                        $params[] = $field === 'price' ? (float)$data[$field] : $data[$field];
                    }
                }

                if (empty($updates)) {
                    sendError('No fields to update', 400);
                }

                $params[] = (int)$id;
                
                try {
                    $stmt = $pdo->prepare("UPDATE products SET " . implode(', ', $updates) . " WHERE product_id = ?");
                    $stmt->execute($params);

                    sendResponse(['success' => true, 'message' => 'Product updated successfully']);
                } catch (PDOException $e) {
                    error_log("Product update error: " . $e->getMessage());
                    sendError('Failed to update product', 500);
                }
                break;

            case 'DELETE':
                if (!validateToken() || !$id) {
                    sendError('Unauthorized or invalid request', 401);
                }

                $user = getUserFromToken();
                if (!$user || $user['type'] !== 'admin') {
                    sendError('Admin access required', 403);
                }

                try {
                    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
                    $stmt->execute([(int)$id]);

                    sendResponse(['success' => true, 'message' => 'Product deleted successfully']);
                } catch (PDOException $e) {
                    error_log("Product deletion error: " . $e->getMessage());
                    sendError('Failed to delete product', 500);
                }
                break;

            default:
                sendError('Method not allowed', 405);
        }
    }
}
