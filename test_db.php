<?php
include 'db_config.php';

try {
    echo "Connected to database.<br>";

    // Check tables
    $tables = ['users', 'products', 'orders', 'order_items', 'messages'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "Table $table exists.<br>";
    }
    echo "All checks passed.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>