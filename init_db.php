<?php
include 'db_config.php';

try {
    // Read and execute the SQLite schema
    $sql = file_get_contents('init_db_sqlite.sql');
    $pdo->exec($sql);

    echo "Database schema created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>