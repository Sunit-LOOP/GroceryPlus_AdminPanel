<?php
session_start();
include 'db_config.php';

if (isset($_SESSION['user_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_name = ? AND user_type = "customer"');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['user_password'])) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
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
    <title>Login - GroceryPlus</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #4CAF50, #81C784); /* Green gradient background */
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100 p-3">
    <div class="card p-4 shadow-lg text-center rounded-4 border-0" style="max-width: 400px; background-color: rgba(255, 255, 255, 0.95);">
        <div class="logo mb-3">
            <img src="images/splash_logo.png" alt="GroceryPlus Logo" class="img-fluid" style="max-width: 80px;">
        </div>
        <h1 class="mb-4 fs-3 fw-bold text-dark">User Login</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mb-3" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3 text-start">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="mb-4 text-start">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Login</button>
        </form>
        <div class="mt-3">
            <p class="mb-0">Don't have an account? <a href="signup.php" class="text-success fw-bold text-decoration-none">Sign up</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
