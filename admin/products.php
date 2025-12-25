<?php
session_start();
include '../db_config.php';

function toNepaliNumeral($number) {
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'];
    $nepali = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९', '.'];
    return str_replace($english, $nepali, $number);
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: products.php?deleted=1');
    exit;
}

// Fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-box"></i>
        Manage Products
    </h1>
    <div class="page-actions">
        <a href="add_product.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add New Product
        </a>
    </div>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    Product deleted successfully!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list"></i> Product Inventory</span>
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Products Found</h4>
                <p class="text-muted">Start by adding your first product to the inventory.</p>
                <a href="add_product.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Your First Product
                </a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="productsTable">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-image"></i> Image</th>
                        <th><i class="fas fa-tag"></i> Name</th>
                        <th><i class="fas fa-align-left"></i> Description</th>
                        <th><i class="fas fa-rupee-sign"></i> Price</th>
                        <th><i class="fas fa-cubes"></i> Stock</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                        <td>
                            <img src="../images/<?php echo htmlspecialchars($product['image'] ?? 'product_icon.png'); ?>"
                                 alt="Product" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td>
                            <span title="<?php echo htmlspecialchars($product['description']); ?>">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>
                                <?php if (strlen($product['description']) > 50): ?>...<?php endif; ?>
                            </span>
                        </td>
                        <td class="fw-bold text-success">रु<?php echo htmlspecialchars(toNepaliNumeral($product['price'])); ?></td>
                        <td>
                            <span class="badge <?php echo $product['stock_quantity'] > 10 ? 'bg-success' : ($product['stock_quantity'] > 0 ? 'bg-warning' : 'bg-danger'); ?>">
                                <?php echo htmlspecialchars($product['stock_quantity']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="products.php?delete=<?php echo $product['product_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                   class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Auto-hide alert after 3 seconds
setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }
}, 3000);
</script>

<?php include 'includes/footer.php'; ?>

<style>
.badge {
    font-size: 0.8em;
}
.btn-group .btn {
    margin-right: 2px;
}
</style>