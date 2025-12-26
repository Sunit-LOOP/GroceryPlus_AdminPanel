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
error_log("DEBUG: Request URI: " . $requestUri);
error_log("DEBUG: Parsed Path: " . $path);
$path = str_replace('/groceryplus/api/index.php/', '', $path);
$path = str_replace('/groceryplus/api/', '', $path);
error_log("DEBUG: Path after str_replace: " . $path);
$pathParts = explode('/', trim($path, '/'));
$endpoint = $pathParts[0] ?? '';
$id = $pathParts[1] ?? null;
error_log("DEBUG: Endpoint: " . $endpoint);
error_log("DEBUG: ID: " . $id);

// Route requests
try {
    switch ($endpoint) {
        case 'auth':
            include 'src/controllers/auth.php';
            AuthController::handleAuth($method, $pdo);
            break;
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
        case 'orders':
            include 'src/controllers/orders.php';
            OrderController::handleOrders($method, $id, $pdo);
            break;
        case 'cart':
            include 'src/controllers/cart.php';
            CartController::handleCart($method, $id, $pdo);
            break;
        case 'favorites':
            include 'src/controllers/favorites.php';
            FavoritesController::handleFavorites($method, $id, $pdo);
            break;
        case 'messages':
            include 'src/controllers/messages.php';
            MessageController::handleMessages($method, $id, $pdo);
            break;
        case 'reviews':
            include 'src/controllers/reviews.php';
            ReviewController::handleReviews($method, $id, $pdo);
            break;
        case 'analytics':
            include 'src/controllers/analytics.php';
            AnalyticsController::handleAnalytics($method, $pdo);
            break;
        case 'notifications':
            include 'src/controllers/notifications.php';
            NotificationController::handleNotifications($method, $id, $pdo);
            break;
        case 'likes':
            include 'src/controllers/likes.php';
            LikesController::handleLikes($method, $pdo);
            break;
        case 'upload':
            include 'src/controllers/upload.php';
            UploadController::handleUpload($method);
            break;
        default:
            sendError('Endpoint not found', 404);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    sendError('Internal server error', 500);
}




















?>