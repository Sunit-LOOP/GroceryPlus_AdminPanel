<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: users.php');
    exit;
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - GroceryPlus Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .header { background: linear-gradient(135deg, #4CAF50, #388E3C); color: white; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .header h1 { margin: 0; font-weight: 500; display: flex; align-items: center; }
        .header h1 img { margin-right: 0.5rem; }
        .header h1 { margin: 0; }
        .back { background: #8BC34A; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; }
        .user-details { background: white; padding: 2rem; margin: 2rem; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .detail { margin-bottom: 1rem; }
        .label { font-weight: bold; color: #000000; }
    </style>
</head>
<body>
    <div class="header">
        <h1><img src="../images/splash_logo.png" alt="Logo">User Details</h1>
        <a href="users.php" class="back">Back to Users</a>
    </div>
    <div class="user-details">
        <div class="detail">
            <span class="label">ID:</span> <?php echo htmlspecialchars($user['id']); ?>
        </div>
        <div class="detail">
            <span class="label">Name:</span> <?php echo htmlspecialchars($user['name']); ?>
        </div>
        <div class="detail">
            <span class="label">Email:</span> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?>
        </div>
        <div class="detail">
            <span class="label">Phone:</span> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
        </div>
        <div class="detail">
            <span class="label">Address:</span> <?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?>
        </div>
        <!-- Add more fields as needed -->
    </div>
</body>
</html>