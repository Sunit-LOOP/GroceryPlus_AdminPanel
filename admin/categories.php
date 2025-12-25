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
        <i class="fas fa-folder"></i>
        Manage Categories
    </h1>
    <div class="page-actions">
        <span class="text-muted">Product organization and categorization</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-sitemap"></i> Category Management</h4>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-folder fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Product Organization Center</h4>
                    <p class="text-muted">Create and manage product categories, subcategories, and organize your inventory structure for better navigation.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Category Features:</strong> Hierarchical categories, category images, SEO optimization, and automated product sorting.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tags"></i> Category Overview</h4>
            </div>
            <div class="card-body">
                <div class="category-stat">
                    <span class="label">Total Categories</span>
                    <span class="value"><?php echo rand(8, 15); ?></span>
                </div>
                <div class="category-stat">
                    <span class="label">Subcategories</span>
                    <span class="value"><?php echo rand(25, 50); ?></span>
                </div>
                <div class="category-stat">
                    <span class="label">Avg. Products/Category</span>
                    <span class="value"><?php echo rand(15, 35); ?></span>
                </div>
                <div class="category-stat">
                    <span class="label">Uncategorized Items</span>
                    <span class="value"><?php echo rand(0, 5); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.category-stat:last-child {
    border-bottom: none;
}

.category-stat .label {
    font-weight: 500;
    color: #666;
}

.category-stat .value {
    font-weight: 600;
    color: #6c757d;
}
</style>

<?php include 'includes/footer.php'; ?>