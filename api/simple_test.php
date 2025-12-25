<?php
// Simple API test
include '../db_config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'message' => 'API is working!',
        'products_count' => $count
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>