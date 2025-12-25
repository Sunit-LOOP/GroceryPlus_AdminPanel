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
        <i class="fas fa-building"></i>
        Vendor Management
    </h1>
    <div class="page-actions">
        <span class="text-muted">Supplier relationships and procurement</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-handshake"></i> Supplier Directory</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-building fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Vendor Relationship Management</h4>
                    <p class="text-muted">Manage supplier partnerships, track performance, negotiate terms, and optimize procurement processes.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Planned Features:</strong> Supplier onboarding, performance tracking, contract management, and automated reordering.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-line"></i> Vendor Analytics</h4>
            </div>
            <div class="card-body">
                <div class="vendor-stat">
                    <span class="label">Active Vendors</span>
                    <span class="value"><?php echo rand(12, 25); ?></span>
                </div>
                <div class="vendor-stat">
                    <span class="label">Avg. Delivery Time</span>
                    <span class="value"><?php echo rand(2, 5); ?> days</span>
                </div>
                <div class="vendor-stat">
                    <span class="label">On-Time Delivery</span>
                    <span class="value"><?php echo rand(85, 95); ?>%</span>
                </div>
                <div class="vendor-stat">
                    <span class="label">Total Spend</span>
                    <span class="value">रु<?php echo number_format(rand(100000, 500000)); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.vendor-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.vendor-stat:last-child {
    border-bottom: none;
}

.vendor-stat .label {
    font-weight: 500;
    color: #666;
}

.vendor-stat .value {
    font-weight: 600;
    color: #6f42c1;
}
</style>

<?php include 'includes/footer.php'; ?>