<?php
// SQLite PDO connection
$dbPath = __DIR__ . '/../../../GroceryPlusDB.db'; // Database path is relative to this file's directory

$dsn = "sqlite:$dbPath";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, null, null, $options);
} catch (PDOException $e) {
    // In production, log this error instead of displaying
    exit('Database connection failed: ' . $e->getMessage());
}
?>
