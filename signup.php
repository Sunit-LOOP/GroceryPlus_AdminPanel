<?php
include __DIR__ . '/admin/includes/header.php';
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_config.php';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name && $email && $password) {
        // Simple validation could be expanded
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)');
        try {
            $stmt->execute([$name, $email, $hash]);
            // After successful signup, redirect to admin dashboard
            header('Location: admin/admin_dashboard.php');
            exit;
        } catch (Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    } else {
        $error = 'All fields are required.';
    }
}
?>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Sign Up</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="" class="mx-auto" style="max-width:400px;">
        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Register</button>
    </form>
</div>
<?php
include __DIR__ . '/admin/includes/footer.php';
?>
