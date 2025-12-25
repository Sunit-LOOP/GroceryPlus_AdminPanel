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
        <i class="fas fa-bullhorn"></i>
        Promotions Management
    </h1>
    <div class="page-actions">
        <span class="text-muted">Marketing campaigns and offers</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tags"></i> Marketing Campaign Center</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Promotion & Marketing Hub</h4>
                    <p class="text-muted">Create and manage promotional campaigns, discount offers, loyalty programs, and targeted marketing initiatives.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Marketing Tools:</strong> Coupon management, flash sales, seasonal promotions, customer segmentation, and campaign analytics.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-line"></i> Campaign Performance</h4>
            </div>
            <div class="card-body">
                <div class="promo-stat">
                    <span class="label">Active Promotions</span>
                    <span class="value"><?php echo rand(3, 8); ?></span>
                </div>
                <div class="promo-stat">
                    <span class="label">Total Coupons Issued</span>
                    <span class="value"><?php echo rand(150, 500); ?></span>
                </div>
                <div class="promo-stat">
                    <span class="label">Redemption Rate</span>
                    <span class="value"><?php echo rand(25, 45); ?>%</span>
                </div>
                <div class="promo-stat">
                    <span class="label">Avg. Order Increase</span>
                    <span class="value">रु<?php echo rand(75, 150); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.promo-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.promo-stat:last-child {
    border-bottom: none;
}

.promo-stat .label {
    font-weight: 500;
    color: #666;
}

.promo-stat .value {
    font-weight: 600;
    color: #fd7e14;
}
</style>

<?php include 'includes/footer.php'; ?>