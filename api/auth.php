<?php
include '../db_config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST' && isset($data['username'], $data['password'])) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_name = ?');
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($data['password'], $user['user_password'])) {
        // In a real application, you should generate and use a proper JWT token.
        $token = base64_encode($user['user_id'] . ':' . $user['user_name']);
        echo json_encode(['token' => $token, 'role' => $user['user_type']]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>