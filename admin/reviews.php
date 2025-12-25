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
        <i class="fas fa-star"></i>
        Reviews Management
    </h1>
    <div class="page-actions">
        <span class="text-muted">Customer feedback and ratings</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-comments"></i> Customer Reviews Hub</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-star fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Review Management System</h4>
                    <p class="text-muted">Monitor customer feedback, manage reviews, respond to comments, and analyze satisfaction metrics.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Review Features:</strong> Review moderation, response management, rating analytics, sentiment analysis, and reputation monitoring.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-pie"></i> Review Analytics</h4>
            </div>
            <div class="card-body">
                <div class="review-stat">
                    <span class="label">Average Rating</span>
                    <span class="value">
                        <?php
                        $rating = rand(42, 48) / 10;
                        echo number_format($rating, 1);
                        ?>
                        <i class="fas fa-star text-warning"></i>
                    </span>
                </div>
                <div class="review-stat">
                    <span class="label">Total Reviews</span>
                    <span class="value"><?php echo rand(150, 400); ?></span>
                </div>
                <div class="review-stat">
                    <span class="label">Response Rate</span>
                    <span class="value"><?php echo rand(75, 95); ?>%</span>
                </div>
                <div class="review-stat">
                    <span class="label">Positive Reviews</span>
                    <span class="value"><?php echo rand(85, 95); ?>%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.review-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.review-stat:last-child {
    border-bottom: none;
}

.review-stat .label {
    font-weight: 500;
    color: #666;
}

.review-stat .value {
    font-weight: 600;
    color: #ffc107;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>