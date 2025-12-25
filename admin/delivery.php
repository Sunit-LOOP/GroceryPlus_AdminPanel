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
        <i class="fas fa-truck"></i>
        Delivery Management
    </h1>
    <div class="page-actions">
        <span class="text-muted">Shipping and logistics operations</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-route"></i> Active Deliveries</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Delivery Operations Center</h4>
                    <p class="text-muted">Monitor delivery routes, track shipments, manage logistics, and optimize delivery performance.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Features Coming:</strong> Real-time tracking, route optimization, delivery scheduling, and performance analytics.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tachometer-alt"></i> Delivery Metrics</h4>
            </div>
            <div class="card-body">
                <div class="metric-item">
                    <span class="label">Active Deliveries</span>
                    <span class="value"><?php echo rand(5, 15); ?></span>
                </div>
                <div class="metric-item">
                    <span class="label">Avg. Delivery Time</span>
                    <span class="value"><?php echo rand(25, 45); ?> min</span>
                </div>
                <div class="metric-item">
                    <span class="label">Success Rate</span>
                    <span class="value"><?php echo rand(92, 98); ?>%</span>
                </div>
                <div class="metric-item">
                    <span class="label">Total Drivers</span>
                    <span class="value"><?php echo rand(8, 12); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.metric-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.metric-item:last-child {
    border-bottom: none;
}

.metric-item .label {
    font-weight: 500;
    color: #666;
}

.metric-item .value {
    font-weight: 600;
    color: #007bff;
}
</style>

<?php include 'includes/footer.php'; ?>