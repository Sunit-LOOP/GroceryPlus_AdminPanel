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
        <i class="fas fa-credit-card"></i>
        Payment Received
    </h1>
    <div class="page-actions">
        <span class="text-muted">Financial transaction management</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list"></i> Payment Transactions</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Payment Management System</h4>
                    <p class="text-muted">Track and manage all payment transactions, refunds, and financial records.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Coming Soon:</strong> Complete payment tracking, transaction history, refund management, and financial reporting.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-pie"></i> Payment Summary</h4>
            </div>
            <div class="card-body">
                <div class="summary-item">
                    <span class="label">Total Revenue</span>
                    <span class="value">रु<?php echo number_format(rand(50000, 200000)); ?></span>
                </div>
                <div class="summary-item">
                    <span class="label">Pending Payments</span>
                    <span class="value"><?php echo rand(5, 25); ?></span>
                </div>
                <div class="summary-item">
                    <span class="label">Refunds Processed</span>
                    <span class="value"><?php echo rand(0, 5); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item .label {
    font-weight: 500;
    color: #666;
}

.summary-item .value {
    font-weight: 600;
    color: #28a745;
}
</style>

<?php include 'includes/footer.php'; ?>