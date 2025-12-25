<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: orders.php?updated=1');
    exit;
}

// Fetch orders with user info
$stmt = $pdo->query("SELECT o.*, u.user_name FROM orders o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.order_id DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-warning',
        'processing' => 'bg-info',
        'shipped' => 'bg-primary',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    return $badges[$status] ?? 'bg-secondary';
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-shopping-cart"></i>
        Manage Orders
    </h1>
    <div class="page-actions">
        <span class="text-muted"><?php echo count($orders); ?> total orders</span>
    </div>
</div>

<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    Order status updated successfully!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list"></i> Order Management</span>
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Search orders...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Orders Found</h4>
                <p class="text-muted">No orders have been placed yet.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="ordersTable">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-hashtag"></i> Order ID</th>
                        <th><i class="fas fa-user"></i> Customer</th>
                        <th><i class="fas fa-calendar"></i> Date</th>
                        <th><i class="fas fa-rupee-sign"></i> Total</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_name'] ?? 'Unknown User'); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($order['order_date']))); ?></td>
                        <td class="fw-bold text-success">रु<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadge($order['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="view_order.php?id=<?php echo $order['order_id']; ?>"
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown" title="Update Status">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <li><button type="submit" name="status" value="pending" class="dropdown-item">Pending</button></li>
                                        <li><button type="submit" name="status" value="processing" class="dropdown-item">Processing</button></li>
                                        <li><button type="submit" name="status" value="shipped" class="dropdown-item">Shipped</button></li>
                                        <li><button type="submit" name="status" value="delivered" class="dropdown-item">Delivered</button></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><button type="submit" name="status" value="cancelled" class="dropdown-item text-danger">Cancelled</button></li>
                                    </form>
                                </ul>
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
    const rows = document.querySelectorAll('#ordersTable tbody tr');

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
.dropdown-menu form button {
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    padding: 0.375rem 1rem;
}
.dropdown-menu form button:hover {
    background-color: var(--bs-dropdown-link-hover-bg);
}
</style>