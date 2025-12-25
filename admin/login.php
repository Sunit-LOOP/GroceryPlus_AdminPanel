<?php
session_start();
include '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_name = ? AND user_type = "admin"');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['user_password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['user_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GroceryPlus</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Roboto', Arial, sans-serif; background: linear-gradient(135deg, #4CAF50, #81C784); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .login-container { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 3rem; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); width: 100%; max-width: 400px; border: 1px solid rgba(255, 255, 255, 0.2); }
        .logo { text-align: center; margin-bottom: 1rem; }
        .logo img { width: 80px; height: 80px; }
        h1 { text-align: center; color: #333; margin-bottom: 2rem; font-weight: 500; }
        form { display: flex; flex-direction: column; }
        label { margin-bottom: 0.5rem; color: #555; font-weight: 500; }
        input { padding: 1rem; margin-bottom: 1.5rem; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; }
        input:focus { outline: none; border-color: #4CAF50; box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1); }
        button { padding: 1rem; background: linear-gradient(135deg, #4CAF50, #388E3C); color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 1rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4); }
        .error { color: #F44336; text-align: center; margin-bottom: 1rem; background: #ffebee; padding: 0.75rem; border-radius: 8px; border-left: 4px solid #F44336; }
        @media (max-width: 480px) { .login-container { padding: 2rem 1.5rem; } }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../images/splash_logo.png" alt="GroceryPlus Logo">
        </div>
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>