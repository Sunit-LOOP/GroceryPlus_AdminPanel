<?php
// Header include for admin pages
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GroceryPlus</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <img src="../images/splash_logo.png" alt="GroceryPlus" class="sidebar-logo">
                <h3>GroceryPlus</h3>
                <span class="admin-badge">Admin Panel</span>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item <?php echo ($current_page == 'products.php' || $current_page == 'add_product.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>">
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="menu-item <?php echo ($current_page == 'users.php' || $current_page == 'view_user.php') ? 'active' : ''; ?>">
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="menu-item <?php echo ($current_page == 'orders.php' || $current_page == 'view_order.php') ? 'active' : ''; ?>">
                    <a href="orders.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="menu-item <?php echo ($current_page == 'messages.php') ? 'active' : ''; ?>">
                    <a href="messages.php">
                        <i class="fas fa-comments"></i>
                        <span>Messages</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="main-content">
