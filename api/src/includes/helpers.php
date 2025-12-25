<?php
// Constants
define('TOKEN_PREFIX_USER', 'user_');
define('TOKEN_PREFIX_ADMIN', 'admin_');
define('API_BASE_URL', 'http://localhost/groceryplus');
define('UPLOAD_DIR', __DIR__ . '/../../../images/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

/**
 * Token validation and generation
 */
function generateToken($userId, $isAdmin = false) {
    $prefix = $isAdmin ? TOKEN_PREFIX_ADMIN : TOKEN_PREFIX_USER;
    $timestamp = time();
    $randomHash = bin2hex(random_bytes(8));
    return $prefix . $userId . '_' . $timestamp . '_' . $randomHash;
}

function getToken() {
    $token = null;
    
    // Check Authorization header first
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
        } else {
            $token = $authHeader;
        }
    }
    
    // Fallback to query parameter
    if (!$token && isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    
    return $token;
}

function validateToken() {
    $token = getToken();
    if (!$token) {
        return false;
    }
    return (strpos($token, TOKEN_PREFIX_USER) === 0 || strpos($token, TOKEN_PREFIX_ADMIN) === 0);
}

function getUserFromToken() {
    $token = getToken();
    
    if (!$token) {
        return null;
    }

    // Extract user ID and type from token
    if (strpos($token, TOKEN_PREFIX_USER) === 0) {
        $parts = explode('_', $token);
        if (isset($parts[1]) && is_numeric($parts[1])) {
            return [
                'id' => (int)$parts[1],
                'type' => 'user',
                'token' => $token
            ];
        }
    } elseif (strpos($token, TOKEN_PREFIX_ADMIN) === 0) {
        $parts = explode('_', $token);
        if (isset($parts[1]) && is_numeric($parts[1])) {
            return [
                'id' => (int)$parts[1],
                'type' => 'admin',
                'token' => $token
            ];
        }
    }

    return null;
}

/**
 * Standard response formatting
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $statusCode >= 200 && $statusCode < 300,
        'status' => $statusCode,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function sendError($message, $statusCode = 400, $errors = null) {
    $response = [
        'success' => false,
        'status' => $statusCode,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if ($errors) {
        $response['errors'] = $errors;
    }
    
    http_response_code($statusCode);
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Input validation helpers
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    return strlen($password) >= 6;
}

function validatePhone($phone) {
    return preg_match('/^[0-9\-\+\s\(\)]{6,}$/', $phone);
}

function sanitizeString($string) {
    return trim(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
}

function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}
