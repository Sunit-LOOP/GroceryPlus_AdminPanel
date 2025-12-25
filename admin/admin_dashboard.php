<?php
include __DIR__ . '/includes/header.php';
?>
<div class="container">
    <img src="../images/admin_banner.png" class="img-fluid mb-4" alt="Admin Banner">

    <!-- Welcome Section -->
    <div class="mb-4">
        <p class="text-muted">Manage your grocery plus application from here</p>
    </div>
    <div class="card mb-4 shadow-sm">
        <div class="card-body text-center">
            <h2 class="card-title">Welcome, Admin!</h2>
        </div>
    </div>
    <!-- Management Sections Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <!-- Manage Products -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/product_icon.png" alt="Products" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Manage Products</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="products.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Manage Categories -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/category_icon.png" alt="Categories" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Manage Categories</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="categories.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Customer Management -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/user_icon.png" alt="Customers" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Customer Management</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="users.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Message Customers -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/message_icon.png" alt="Messages" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Message Customers</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="messages.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Order Management -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/order_icon.png" alt="Orders" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Order Management</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="orders.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Analytics Dashboard -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/analytics_icon.png" alt="Analytics" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Analytics Dashboard</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="analytics.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Promotions Management -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/promo_icon.png" alt="Promotions" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Promotions</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="promotions.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Reviews Management -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/review_icon.png" alt="Reviews" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Reviews Management</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="reviews.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Payment Received -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/card_icon.png" alt="Payments" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Payment Received</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="payments.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Delivery Management -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/delivery_truck_icon.png" alt="Delivery" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Delivery Management</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="delivery.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <!-- Vendor Management -->
        <div class="col">
            <div class="card h-100 text-center shadow-sm">
                <div class="card-body">
                    <img src="../images/vendor_icon.png" alt="Vendors" class="mb-3" style="width:48px;height:48px;">
                    <h5 class="card-title">Vendor Management</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="vendors.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Logout Button -->
    <div class="mt-5">
        <a href="logout.php" class="btn btn-danger w-100">Logout</a>
    </div>
</div>
<?php
include __DIR__ . '/includes/footer.php';
?>
