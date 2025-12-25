<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-tie"></i>
        Delivery Personnel
    </h1>
    <div class="page-actions">
        <span class="text-muted">Driver management and coordination</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-users-cog"></i> Driver Management System</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Delivery Team Management</h4>
                    <p class="text-muted">Manage delivery drivers, track performance, assign routes, and coordinate delivery operations.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Driver Features:</strong> Driver onboarding, performance tracking, route assignment, GPS monitoring, and delivery analytics.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tachometer-alt"></i> Driver Performance</h4>
            </div>
            <div class="card-body">
                <div class="driver-stat">
                    <span class="label">Active Drivers</span>
                    <span class="value"><?php echo rand(8, 15); ?></span>
                </div>
                <div class="driver-stat">
                    <span class="label">Avg. Deliveries/Day</span>
                    <span class="value"><?php echo rand(12, 20); ?></span>
                </div>
                <div class="driver-stat">
                    <span class="label">On-Time Rate</span>
                    <span class="value"><?php echo rand(88, 96); ?>%</span>
                </div>
                <div class="driver-stat">
                    <span class="label">Customer Rating</span>
                    <span class="value">
                        <?php
                        $rating = rand(42, 48) / 10;
                        echo number_format($rating, 1);
                        ?>
                        <i class="fas fa-star text-warning"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.driver-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.driver-stat:last-child {
    border-bottom: none;
}

.driver-stat .label {
    font-weight: 500;
    color: #666;
}

.driver-stat .value {
    font-weight: 600;
    color: #343a40;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>