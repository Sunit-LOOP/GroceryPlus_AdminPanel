<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Advanced Analytics & Stats
try {
    // Core metrics
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?? 0;
    $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0;
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?? 0;
    $total_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn() ?? 0;
    $today_new_users = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = DATE('now')")->fetchColumn() ?? 0;

    // Today's metrics
    $today_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = DATE('now')")->fetchColumn() ?? 0;
    $yesterday_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = DATE('now', '-1 day')")->fetchColumn() ?? 0;
    $today_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = DATE('now') AND status = 'delivered'")->fetchColumn() ?? 0;

    // Weekly metrics (last 7 days)
    $week_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE created_at >= datetime('now', '-7 days')")->fetchColumn() ?? 0;
    $week_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE created_at >= datetime('now', '-7 days') AND status = 'delivered'")->fetchColumn() ?? 0;
    $prev_week_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE created_at >= datetime('now', '-14 days') AND created_at < datetime('now', '-7 days') AND status = 'delivered'")->fetchColumn() ?? 0;


    // Status breakdown
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn() ?? 0;
    $processing_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn() ?? 0;
    $shipped_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'shipped'")->fetchColumn() ?? 0;
    $delivered_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn() ?? 0;

    // Top products (by order count)
    $top_products = $pdo->query("
        SELECT p.product_name, COUNT(oi.order_item_id) as order_count,
               SUM(oi.quantity) as total_quantity
        FROM products p
        JOIN order_items oi ON p.product_id = oi.product_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.status != 'cancelled'
        GROUP BY p.product_id
        ORDER BY order_count DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC) ?? [];

    // Recent orders
    $recent_orders = $pdo->query("
        SELECT o.order_id, o.total_amount, o.status, o.created_at,
               u.user_name, COUNT(oi.order_item_id) as item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id
        ORDER BY o.created_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC) ?? [];

    // Low stock alerts
    $low_stock_products = $pdo->query("
        SELECT product_name, stock_quantity
        FROM products
        WHERE stock_quantity <= 10
        ORDER BY stock_quantity ASC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC) ?? [];

} catch (Exception $e) {
    // Default values if database error
    $total_users = $total_products = $total_orders = $total_messages = 0;
    $today_orders = $yesterday_orders = $today_revenue = $week_orders = $week_revenue = $prev_week_revenue = 0;
    $pending_orders = $processing_orders = $shipped_orders = $delivered_orders = 0;
    $top_products = $recent_orders = $low_stock_products = [];
}

// Helper functions
function formatCurrency($amount) {
    return 'रु' . number_format((float)$amount, 0);
}

function getStatusColor($status) {
    return match($status) {
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        default => 'secondary'
    };
}

function calculatePercentageChange($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return (($current - $previous) / $previous) * 100;
}

$weekly_revenue_change = calculatePercentageChange($week_revenue, $prev_week_revenue);
$today_orders_change = calculatePercentageChange($today_orders, $yesterday_orders);


include 'includes/header.php';
?>

<!-- Welcome Header -->
<div class="welcome-header">
    <div class="welcome-content">
        <div class="welcome-text">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Monitor and manage your grocery business performance</p>
        </div>
        <div class="header-actions">
            <div class="current-time">
                <i class="fas fa-clock"></i>
                <span id="current-time"><?php echo date('l, F j, Y - g:i A'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Dashboard -->
<div class="kpi-dashboard">
    <div class="kpi-grid">
        <!-- Total Revenue -->
        <div class="kpi-card revenue">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="kpi-period">
                    <span>This Week</span>
                </div>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo formatCurrency($week_revenue); ?></div>
                <div class="kpi-label">Weekly Revenue</div>
                <div class="kpi-change <?php echo $weekly_revenue_change >= 0 ? 'positive' : 'negative'; ?>">
                    <i class="fas fa-arrow-<?php echo $weekly_revenue_change >= 0 ? 'up' : 'down'; ?>"></i>
                    <?php echo sprintf('%+.1f%%', $weekly_revenue_change); ?>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="kpi-card orders">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="kpi-period">
                    <span>Today</span>
                </div>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo $today_orders; ?></div>
                <div class="kpi-label">Orders Today</div>
                <div class="kpi-change <?php echo $today_orders_change >= 0 ? 'positive' : 'negative'; ?>">
                    <i class="fas fa-arrow-<?php echo $today_orders_change >= 0 ? 'up' : 'down'; ?>"></i>
                    <?php echo sprintf('%+.1f%%', $today_orders_change); ?>
                </div>
            </div>
        </div>

        <!-- Active Customers -->
        <div class="kpi-card customers">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="kpi-period">
                    <span>Total</span>
                </div>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo $total_users; ?></div>
                <div class="kpi-label">Active Customers</div>
                <div class="kpi-change positive">
                    <i class="fas fa-user-plus"></i> +<?php echo $today_new_users; ?> new
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="kpi-card alerts">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="kpi-period">
                    <span>Urgent</span>
                </div>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo $pending_orders + $total_messages; ?></div>
                <div class="kpi-label">Pending Tasks</div>
                <div class="kpi-change warning">
                    <i class="fas fa-clock"></i> Attention needed
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Panel -->
<div class="quick-access-section">
    <div class="section-header">
        <h3><i class="fas fa-rocket"></i> Quick Access</h3>
        <p>Most frequently used administrative tools</p>
    </div>
    <div class="quick-access-grid">
        <!-- 🛒 PRODUCT MANAGEMENT -->
        <a href="add_product.php" class="quick-access-item primary">
            <div class="access-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Add Product</span>
                <span class="access-subtitle">Create new item</span>
            </div>
        </a>

        <a href="products.php" class="quick-access-item primary">
            <div class="access-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Manage Products</span>
                <span class="access-subtitle"><?php echo $total_products; ?> products</span>
            </div>
        </a>

        <!-- 🛒 ORDER MANAGEMENT -->
        <a href="orders.php" class="quick-access-item success">
            <div class="access-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Order Management</span>
                <span class="access-subtitle">Process orders</span>
            </div>
        </a>

        <!-- 👥 CUSTOMER MANAGEMENT -->
        <a href="users.php" class="quick-access-item success">
            <div class="access-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Customer Management</span>
                <span class="access-subtitle"><?php echo $total_users; ?> customers</span>
            </div>
        </a>

        <a href="messages.php" class="quick-access-item info">
            <div class="access-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Customer Messages</span>
                <span class="access-subtitle"><?php echo $total_messages; ?> unread</span>
            </div>
        </a>

        <!-- 💰 FINANCIAL MANAGEMENT -->
        <a href="payments.php" class="quick-access-item warning">
            <div class="access-icon">
                <img src="../images/card_icon.png" alt="Payments">
            </div>
            <div class="access-content">
                <span class="access-title">Payment Received</span>
                <span class="access-subtitle">Financial tracking</span>
            </div>
        </a>

        <!-- 🚚 DELIVERY & LOGISTICS -->
        <a href="delivery.php" class="quick-access-item secondary">
            <div class="access-icon">
                <img src="../images/delivery_truck_icon.png" alt="Delivery">
            </div>
            <div class="access-content">
                <span class="access-title">Delivery Management</span>
                <span class="access-subtitle">Shipping & logistics</span>
            </div>
        </a>

        <!-- 🏭 SUPPLY CHAIN -->
        <a href="vendors.php" class="quick-access-item dark">
            <div class="access-icon">
                <img src="../images/vendor_icon.png" alt="Vendors">
            </div>
            <div class="access-content">
                <span class="access-title">Vendor Management</span>
                <span class="access-subtitle">Supplier relations</span>
            </div>
        </a>

        <!-- 📊 ANALYTICS & REPORTS -->
        <a href="analytics.php" class="quick-access-item info">
            <div class="access-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Analytics Dashboard</span>
                <span class="access-subtitle">Business insights</span>
            </div>
        </a>

        <!-- 🎯 MARKETING -->
        <a href="promotions.php" class="quick-access-item warning">
            <div class="access-icon">
                <img src="../images/promo_icon.png" alt="Promotions">
            </div>
            <div class="access-content">
                <span class="access-title">Promotions</span>
                <span class="access-subtitle">Marketing campaigns</span>
            </div>
        </a>

        <!-- ⭐ REVIEWS -->
        <a href="reviews.php" class="quick-access-item info">
            <div class="access-icon">
                <img src="../images/review_icon.png" alt="Reviews">
            </div>
            <div class="access-content">
                <span class="access-title">Reviews Management</span>
                <span class="access-subtitle">Customer feedback</span>
            </div>
        </a>

        <!-- 📂 CATEGORIES -->
        <a href="categories.php" class="quick-access-item secondary">
            <div class="access-icon">
                <img src="../images/category_icon.png" alt="Categories">
            </div>
            <div class="access-content">
                <span class="access-title">Manage Categories</span>
                <span class="access-subtitle">Product organization</span>
            </div>
        </a>

        <!-- 👨‍💼 DELIVERY PERSONNEL -->
        <a href="delivery_personnel.php" class="quick-access-item dark">
            <div class="access-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="access-content">
                <span class="access-title">Delivery Personnel</span>
                <span class="access-subtitle">Driver management</span>
            </div>
        </a>
    </div>
</div>

<!-- Analytics Section -->
<div class="analytics-section">
    <div class="analytics-grid">
        <!-- Order Status Chart -->
        <div class="analytics-card">
            <div class="card-header">
                <h4><i class="fas fa-chart-pie"></i> Order Status Distribution</h4>
            </div>
            <div class="card-body">
            <div class="chart-container">
                <?php if ($total_orders > 0): ?>
                <canvas id="orderStatusChart" width="300" height="200"></canvas>
                <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-chart-pie"></i>
                    <p>No order data to display</p>
                </div>
                <?php endif; ?>
            </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-color pending"></span>
                        <span>Pending (<?php echo $pending_orders; ?>)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color processing"></span>
                        <span>Processing (<?php echo $processing_orders; ?>)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color shipped"></span>
                        <span>Shipped (<?php echo $shipped_orders; ?>)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color delivered"></span>
                        <span>Delivered (<?php echo $delivered_orders; ?>)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="analytics-card">
            <div class="card-header">
                <h4><i class="fas fa-trophy"></i> Top Performing Products</h4>
            </div>
            <div class="card-body">
                <div class="top-products">
                    <?php foreach ($top_products as $index => $product): ?>
                    <div class="product-rank">
                        <div class="rank-number"><?php echo $index + 1; ?></div>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                            <div class="product-stats">
                                <?php echo $product['order_count']; ?> orders • <?php echo $product['total_quantity']; ?> units
                            </div>
                        </div>
                        <div class="rank-badge <?php echo $index < 3 ? 'top' : ''; ?>">
                            #<?php echo $index + 1; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($top_products)): ?>
                    <div class="no-data">
                        <i class="fas fa-chart-bar"></i>
                        <p>No order data available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="analytics-card">
            <div class="card-header">
                <h4><i class="fas fa-clock"></i> Recent Orders</h4>
                <a href="orders.php" class="view-all-link">View All</a>
            </div>
            <div class="card-body">
                <div class="recent-orders">
                    <?php foreach ($recent_orders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-id">#<?php echo $order['order_id']; ?></div>
                            <div class="order-customer"><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></div>
                            <div class="order-meta">
                                <?php echo $order['item_count']; ?> items • <?php echo formatCurrency($order['total_amount']); ?>
                            </div>
                        </div>
                        <div class="order-status">
                            <span class="status-badge <?php echo getStatusColor($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <div class="order-time"><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent_orders)): ?>
                    <div class="no-data">
                        <i class="fas fa-shopping-cart"></i>
                        <p>No recent orders</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alerts & Notifications -->
        <div class="analytics-card alerts">
            <div class="card-header">
                <h4><i class="fas fa-bell"></i> Alerts & Notifications</h4>
            </div>
            <div class="card-body">
                <!-- Pending Orders Alert -->
                <?php if ($pending_orders > 0): ?>
                <div class="alert-item warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <div class="alert-title"><?php echo $pending_orders; ?> orders pending processing</div>
                        <div class="alert-action">
                            <a href="orders.php?status=pending">Process Now</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Low Stock Alert -->
                <?php if (!empty($low_stock_products)): ?>
                <div class="alert-item danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="alert-content">
                        <div class="alert-title"><?php echo count($low_stock_products); ?> products low on stock</div>
                        <div class="alert-action">
                            <a href="products.php?filter=low_stock">View Inventory</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Unread Messages -->
                <?php if ($total_messages > 0): ?>
                <div class="alert-item info">
                    <i class="fas fa-envelope"></i>
                    <div class="alert-content">
                        <div class="alert-title"><?php echo $total_messages; ?> unread customer messages</div>
                        <div class="alert-action">
                            <a href="messages.php">Check Messages</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- System Health -->
                <div class="alert-item success">
                    <i class="fas fa-check-circle"></i>
                    <div class="alert-content">
                        <div class="alert-title">All systems operational</div>
                        <div class="alert-action">
                            <span>System Status: Good</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
