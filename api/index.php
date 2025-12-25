<?php
// GroceryPlus API - Complete Sync System
// Supports both Android App and Web Application

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include '../db_config.php';

// Simple token validation (simplified for compatibility)
function validateToken() {
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? $_GET['token'] ?? null;

    if (!$token) {
        return false;
    }

    // Simple validation - in production use proper JWT
    return strpos($token, 'user_') === 0 || strpos($token, 'admin_') === 0;
}

function generateToken($userId, $isAdmin = false) {
    $prefix = $isAdmin ? 'admin_' : 'user_';
    return $prefix . $userId . '_' . time() . '_' . substr(md5(uniqid()), 0, 8);
}

function getUserFromToken() {
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? $_GET['token'] ?? null;

    if (!$token) return null;

    // Extract user ID from token
    if (strpos($token, 'user_') === 0) {
        $parts = explode('_', $token);
        return ['id' => $parts[1], 'type' => 'user'];
    } elseif (strpos($token, 'admin_') === 0) {
        $parts = explode('_', $token);
        return ['id' => $parts[1], 'type' => 'admin'];
    }

    return null;
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function sendError($message, $statusCode = 400) {
    sendResponse(['error' => true, 'message' => $message], $statusCode);
}

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
            handleAuth($method, $pdo);
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

function handleAuth($method, $pdo) {
    if ($method !== 'POST') {
        sendError('Method not allowed', 405);
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['email'], $data['password'])) {
        sendError('Email and password required');
    }

    // Check for admin login
    if ($data['email'] === 'admin@groceryplus.com' && $data['password'] === 'admin123') {
        sendResponse([
            'token' => generateToken(1, true),
            'user' => [
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@groceryplus.com',
                'type' => 'admin'
            ]
        ]);
    }

    // Check user login
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['user_password'] ?? '')) {
        sendError('Invalid credentials', 401);
    }

    sendResponse([
        'token' => generateToken($user['user_id']),
        'user' => [
            'id' => $user['user_id'],
            'name' => $user['user_name'],
            'email' => $user['user_email'],
            'phone' => $user['user_phone'],
            'type' => 'user'
        ]
    ]);
}

function handleRegister($method, $pdo) {
    if ($method !== 'POST') {
        sendError('Method not allowed', 405);
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['email'], $data['password'], $data['phone'])) {
        sendError('All fields required');
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE user_email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        sendError('Email already registered', 409);
    }

    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (user_name, user_email, user_phone, user_password, user_type) VALUES (?, ?, ?, ?, 'customer')");
    $stmt->execute([$data['name'], $data['email'], $data['phone'], $hashedPassword]);

    $userId = $pdo->lastInsertId();

    sendResponse([
        'token' => generateToken($userId),
        'user' => [
            'id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'type' => 'user'
        ]
    ], 201);
}

function handleProducts($method, $id, $pdo) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("
                    SELECT p.*, c.category_name, v.vendor_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                    WHERE p.product_id = ?
                ");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $product['image_url'] = $product['image'] ? "http://localhost/groceryplus/images/{$product['image']}" : null;
                    sendResponse($product);
                } else {
                    sendError('Product not found', 404);
                }
            } else {
                $category = $_GET['category'] ?? null;
                $search = $_GET['search'] ?? null;
                $limit = (int)($_GET['limit'] ?? 50);
                $offset = (int)($_GET['offset'] ?? 0);

                $query = "
                    SELECT p.*, c.category_name, v.vendor_name
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
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

                $query .= " ORDER BY p.product_id DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Add image URLs
                foreach ($products as &$product) {
                    $product['image_url'] = $product['image'] ? "http://localhost/groceryplus/images/{$product['image']}" : null;
                }

                sendResponse(['products' => $products, 'count' => count($products)]);
            }
            break;

        case 'POST':
            if (!validateToken()) {
                sendError('Unauthorized', 401);
            }

            $user = getUserFromToken();
            if ($user['type'] !== 'admin') {
                sendError('Admin access required', 403);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['product_name'], $data['price'])) {
                sendError('Product name and price required');
            }

            $stmt = $pdo->prepare("
                INSERT INTO products (product_name, category_id, price, description, stock_quantity, vendor_id, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['product_name'],
                $data['category_id'] ?? 1,
                $data['price'],
                $data['description'] ?? '',
                $data['stock_quantity'] ?? 0,
                $data['vendor_id'] ?? 1,
                $data['image'] ?? null
            ]);

            sendResponse(['product_id' => $pdo->lastInsertId()], 201);
            break;

        case 'PUT':
            if (!validateToken() || !$id) {
                sendError('Unauthorized or invalid request', 401);
            }

            $user = getUserFromToken();
            if ($user['type'] !== 'admin') {
                sendError('Admin access required', 403);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $updates = [];
            $params = [];

            foreach (['product_name', 'category_id', 'price', 'description', 'stock_quantity', 'vendor_id', 'image'] as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            if (empty($updates)) {
                sendError('No fields to update');
            }

            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE products SET " . implode(', ', $updates) . " WHERE product_id = ?");
            $stmt->execute($params);

            sendResponse(['success' => true]);
            break;

        case 'DELETE':
            if (!validateToken() || !$id) {
                sendError('Unauthorized or invalid request', 401);
            }

            $user = getUserFromToken();
            if ($user['type'] !== 'admin') {
                sendError('Admin access required', 403);
            }

            $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$id]);

            sendResponse(['success' => true]);
            break;

        default:
            sendError('Method not allowed', 405);
    }
}

function handleCategories($method, $id, $pdo) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
                $stmt->execute([$id]);
                sendResponse($stmt->fetch(PDO::FETCH_ASSOC));
            } else {
                $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
                sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;

        case 'POST':
            if (!validateToken()) sendError('Unauthorized', 401);

            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO categories (category_name, category_description) VALUES (?, ?)");
            $stmt->execute([$data['category_name'], $data['category_description'] ?? '']);
            sendResponse(['category_id' => $pdo->lastInsertId()], 201);
            break;

        default:
            sendError('Method not allowed', 405);
    }
}

function handleUsers($method, $id, $pdo) {
    if (!validateToken()) {
        sendError('Unauthorized', 401);
    }

    $user = getUserFromToken();
    if ($user['type'] !== 'admin' && $method !== 'GET') {
        sendError('Admin access required', 403);
    }

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT user_id, user_name, user_email, user_phone, user_type, created_at FROM users WHERE user_id = ?");
                $stmt->execute([$id]);
                sendResponse($stmt->fetch(PDO::FETCH_ASSOC));
            } else {
                $stmt = $pdo->query("SELECT user_id, user_name, user_email, user_phone, user_type, created_at FROM users ORDER BY created_at DESC");
                sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $updates = [];
            $params = [];

            foreach (['user_name', 'user_email', 'user_phone'] as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            $params[] = $id;
            $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = ?");
            $stmt->execute($params);

            sendResponse(['success' => true]);
            break;

        default:
            sendError('Method not allowed', 405);
    }
}

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
                $stmt->execute([$id]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($order) {
                    // Get order items
                    $stmt = $pdo->prepare("
                        SELECT oi.*, p.product_name, p.image
                        FROM order_items oi
                        LEFT JOIN products p ON oi.product_id = p.product_id
                        WHERE oi.order_id = ?
                    ");
                    $stmt->execute([$id]);
                    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Add image URLs
                    foreach ($order['items'] as &$item) {
                        $item['image_url'] = $item['image'] ? "http://localhost/groceryplus/images/{$item['image']}" : null;
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
                    $stmt->execute([$user['id']]);
                } else {
                    $query .= " GROUP BY o.order_id ORDER BY o.created_at DESC";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                }

                sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['user_id'], $data['items'])) {
                sendError('User ID and items required');
            }

            // Calculate total
            $total = 0;
            foreach ($data['items'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create order
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, total_amount, delivery_fee, status, order_date)
                VALUES (?, ?, ?, 'pending', datetime('now'))
            ");
            $stmt->execute([$data['user_id'], $total, $data['delivery_fee'] ?? 0]);
            $orderId = $pdo->lastInsertId();

            // Add items
            foreach ($data['items'] as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }

            sendResponse(['order_id' => $orderId], 201);
            break;

        case 'PUT':
            if (!$id) sendError('Order ID required');

            $data = json_decode(file_get_contents('php://input'), true);

            if ($user['type'] === 'admin') {
                $stmt = $pdo->prepare("UPDATE orders SET status = ?, shipped_date = ? WHERE order_id = ?");
                $shippedDate = $data['status'] === 'shipped' ? date('Y-m-d H:i:s') : null;
                $stmt->execute([$data['status'], $shippedDate, $id]);
            } else {
                // Users can only cancel pending orders
                if ($data['status'] === 'cancelled') {
                    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ? AND user_id = ? AND status = 'pending'");
                    $stmt->execute([$id, $user['id']]);
                }
            }

            sendResponse(['success' => true]);
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
    if ($user['type'] !== 'admin') {
        sendError('Admin access required', 403);
    }

    if (!isset($_FILES['image'])) {
        sendError('No image file provided');
    }

    $file = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowedTypes)) {
        sendError('Invalid file type. Only JPEG, PNG, and GIF allowed');
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        sendError('File too large. Maximum size is 5MB');
    }

    $uploadDir = '../images/';
    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        sendResponse([
            'success' => true,
            'image_url' => "http://localhost/groceryplus/images/$fileName",
            'filename' => $fileName
        ]);
    } else {
        sendError('Upload failed');
    }
}
?>