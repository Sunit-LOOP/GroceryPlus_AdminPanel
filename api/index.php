<?php
/**
 * GroceryPlus REST API
 * Version: 1.0
 * Supports Android App, iOS App, and Web Application
 * 
 * Base URL: http://localhost/groceryplus/api/
 * Authentication: Bearer Token in Authorization header
 * Content-Type: application/json
 */

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('X-API-Version: 1.0');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include 'src/includes/db.php';

include 'src/includes/helpers.php';

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/groceryplus/api/', '', $path);
$pathParts = explode('/', trim($path, '/'));
$endpoint = $pathParts[0] ?? '';
$id = $pathParts[1] ?? null;

// Route requests
try {
    switch ($endpoint) {
        case 'auth':
            include 'src/controllers/auth.php';
            AuthController::handleAuth($method, $pdo);
            break;
        case 'register':
            handleRegister($method, $pdo);
            break;
        case 'products':
            handleProducts($method, $id, $pdo);
            break;
        case 'categories':
            handleCategories($method, $id, $pdo);
            break;
        case 'users':
            handleUsers($method, $id, $pdo);
            break;
        case 'orders':
            handleOrders($method, $id, $pdo);
            break;
        case 'cart':
            handleCart($method, $id, $pdo);
            break;
        case 'favorites':
            handleFavorites($method, $id, $pdo);
            break;
        case 'messages':
            handleMessages($method, $id, $pdo);
            break;
        case 'reviews':
            handleReviews($method, $id, $pdo);
            break;
        case 'analytics':
            handleAnalytics($method, $pdo);
            break;
        case 'notifications':
            handleNotifications($method, $id, $pdo);
            break;
        case 'upload':
            handleUpload($method);
            break;
        default:
            sendError('Endpoint not found', 404);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendError('Internal server error', 500);
}



        case 'register':
            include 'src/controllers/register.php';
            RegisterController::handleRegister($method, $pdo);
            break;

        case 'products':
            include 'src/controllers/products.php';
            ProductController::handleProducts($method, $id, $pdo);
            break;

        case 'categories':
            include 'src/controllers/categories.php';
            CategoryController::handleCategories($method, $id, $pdo);
            break;

        case 'users':
            include 'src/controllers/users.php';
            UserController::handleUsers($method, $id, $pdo);
            break;

function handleOrders($method, $id, $pdo) {
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

function handleCart($method, $id, $pdo) {
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

function handleFavorites($method, $id, $pdo) {
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

function handleMessages($method, $id, $pdo) {
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

function handleReviews($method, $id, $pdo) {
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

function handleAnalytics($method, $pdo) {
    if ($method !== 'GET') {
        sendError('Method not allowed', 405);
    }

    if (!validateToken()) {
        sendError('Unauthorized', 401);
    }

    $user = getUserFromToken();
    if ($user['type'] !== 'admin') {
        sendError('Admin access required', 403);
    }

    // Get various analytics
    $analytics = [];

    // Revenue analytics
    $stmt = $pdo->query("
        SELECT
            SUM(CASE WHEN DATE(created_at) = DATE('now') THEN total_amount ELSE 0 END) as today_revenue,
            SUM(CASE WHEN created_at >= datetime('now', '-7 days') THEN total_amount ELSE 0 END) as week_revenue,
            AVG(total_amount) as avg_order_value
        FROM orders
        WHERE status = 'delivered'
    ");
    $analytics['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Order analytics
    $stmt = $pdo->query("
        SELECT
            COUNT(CASE WHEN DATE(created_at) = DATE('now') THEN 1 END) as today_orders,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
            COUNT(CASE WHEN status = 'delivered' THEN 1 END) as completed_orders
        FROM orders
    ");
    $analytics['orders'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // User analytics
    $stmt = $pdo->query("
        SELECT
            COUNT(*) as total_users,
            COUNT(CASE WHEN DATE(created_at) >= datetime('now', '-30 days') THEN 1 END) as new_users
        FROM users
    ");
    $analytics['users'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Product analytics
    $stmt = $pdo->query("
        SELECT
            COUNT(*) as total_products,
            COUNT(CASE WHEN stock_quantity <= 10 THEN 1 END) as low_stock
        FROM products
    ");
    $analytics['products'] = $stmt->fetch(PDO::FETCH_ASSOC);

    sendResponse($analytics);
}

function handleNotifications($method, $id, $pdo) {
    if (!validateToken()) {
        sendError('Unauthorized', 401);
    }

    $user = getUserFromToken();

    switch ($method) {
        case 'GET':
            $stmt = $pdo->prepare("
                SELECT * FROM notifications
                WHERE user_id = ? OR user_id IS NULL
                ORDER BY created_at DESC
                LIMIT 20
            ");
            $stmt->execute([$user['id']]);
            sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
            if ($user['type'] !== 'admin') {
                sendError('Admin access required', 403);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, title, message)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$data['user_id'] ?? null, $data['title'], $data['message']]);
            sendResponse(['success' => true], 201);
            break;

        case 'PUT':
            if (!$id) sendError('Notification ID required');

            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
            $stmt->execute([$id, $user['id']]);

            sendResponse(['success' => true]);
            break;

        default:
            sendError('Method not allowed', 405);
    }
}

function handleUpload($method) {
    if ($method !== 'POST') {
        sendError('Method not allowed', 405);
    }

    if (!validateToken()) {
        sendError('Unauthorized', 401);
    }

    $user = getUserFromToken();
    if (!$user || $user['type'] !== 'admin') {
        sendError('Admin access required', 403);
    }

    if (!isset($_FILES['image'])) {
        sendError('No image file provided', 400);
    }

    $file = $_FILES['image'];

    // Validate file type
    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        sendError('Invalid file type. Only JPEG, PNG, GIF, and WebP allowed', 422);
    }

    // Validate file size
    if ($file['size'] > MAX_FILE_SIZE) {
        sendError('File too large. Maximum size is 5MB', 422);
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("File upload error: " . $file['error']);
        sendError('Upload failed. Please try again.', 500);
    }

    // Create upload directory if doesn't exist
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    // Generate unique filename
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = uniqid('img_', true) . '.' . $ext;
    $uploadPath = UPLOAD_DIR . $fileName;

    // Validate file can be read as image
    if (!getimagesize($file['tmp_name'])) {
        sendError('Invalid image file', 422);
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        error_log("Failed to move uploaded file: $uploadPath");
        sendError('Upload failed. Please try again.', 500);
    }

    // Set proper permissions
    chmod($uploadPath, 0644);

    sendResponse([
        'success' => true,
        'image_url' => API_BASE_URL . "/images/$fileName",
        'filename' => $fileName,
        'size' => filesize($uploadPath)
    ], 200);
}
?>