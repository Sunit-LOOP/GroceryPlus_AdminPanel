<?php
include '../db_config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST' && isset($data['username'], $data['password'])) {
    // Simple auth - replace with proper hashing
    if ($data['username'] === 'admin' && $data['password'] === 'admin123') {
        echo json_encode(['token' => 'admin_token', 'role' => 'admin']);
    } else {
        echo json_encode(['error' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
?>