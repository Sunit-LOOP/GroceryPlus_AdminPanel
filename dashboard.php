<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - GroceryPlus</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Roboto', Arial, sans-serif; background-color: #f4f6f9; margin: 0; }
        .header { background-color: #4CAF50; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .header a { color: white; text-decoration: none; }
        .container { padding: 2rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</h1>
        <a href="logout.php">Logout</a>
    </div>
    <div class="container">
        <h2>Your Dashboard</h2>
        <p>This is your user dashboard. You can add more content here.</p>
    </div>
</body>
</html>
