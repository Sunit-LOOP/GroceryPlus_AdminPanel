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
        <i class="fas fa-chart-bar"></i>
        Analytics Dashboard
    </h1>
    <div class="page-actions">
        <span class="text-muted">Business intelligence and insights</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-area"></i> Business Intelligence Center</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Advanced Analytics Platform</h4>
                    <p class="text-muted">Comprehensive business intelligence with detailed reports, trend analysis, and predictive insights.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Analytics Features:</strong> Sales forecasting, customer behavior analysis, inventory optimization, and performance dashboards.
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Sales Growth</h5>
                        <h3 class="text-success">+<?php echo rand(15, 35); ?>%</h3>
                        <small class="text-muted">vs last month</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Customer Retention</h5>
                        <h3 class="text-primary"><?php echo rand(75, 90); ?>%</h3>
                        <small class="text-muted">loyal customers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-trophy"></i> Top Insights</h4>
            </div>
            <div class="card-body">
                <div class="insight-item">
                    <span class="insight-label">Peak Hours</span>
                    <span class="insight-value">2-4 PM</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Top Category</span>
                    <span class="insight-value">Fruits</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Avg. Order Value</span>
                    <span class="insight-value">रु<?php echo rand(450, 750); ?></span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Repeat Customers</span>
                    <span class="insight-value"><?php echo rand(60, 80); ?>%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.insight-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.insight-item:last-child {
    border-bottom: none;
}

.insight-item .insight-label {
    font-weight: 500;
    color: #666;
}

.insight-item .insight-value {
    font-weight: 600;
    color: #28a745;
}
</style>

<?php include 'includes/footer.php'; ?>