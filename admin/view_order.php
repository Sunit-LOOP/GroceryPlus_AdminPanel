<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: orders.php');
    exit;
}

// Fetch order
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email, u.phone FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Fetch order items (assuming order_items table)
$stmt = $pdo->prepare("SELECT oi.*, p.name as product_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - GroceryPlus Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .header { background: linear-gradient(135deg, #4CAF50, #388E3C); color: white; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .header h1 { margin: 0; font-weight: 500; display: flex; align-items: center; }
        .header h1 img { margin-right: 0.5rem; }
        .header h1 { margin: 0; }
        .back { background: #8BC34A; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; }
        .order-details { background: white; padding: 2rem; margin: 2rem; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .section { margin-bottom: 2rem; }
        .section h3 { color: #000000; border-bottom: 2px solid #4CAF50; padding-bottom: 0.5rem; }
        .detail { margin-bottom: 0.5rem; }
        .label { font-weight: bold; color: #000000; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #81C784; color: #000000; }
    </style>
</head>
<body>
    <div class="header">
        <h1><img src="../images/splash_logo.png" alt="Logo">Order Details</h1>
        <a href="orders.php" class="back">Back to Orders</a>
    </div>
    <div class="order-details">
        <div class="section">
            <h3>Order Information</h3>
            <div class="detail">
                <span class="label">Order ID:</span> <?php echo htmlspecialchars($order['id']); ?>
            </div>
            <div class="detail">
                <span class="label">Status:</span> <?php echo htmlspecialchars(ucfirst($order['status'] ?? 'Pending')); ?>
            </div>
            <div class="detail">
                <span class="label">Total:</span> रु<?php echo htmlspecialchars($order['total'] ?? '0.00'); ?>
            </div>
            <div class="detail">
                <span class="label">Date:</span> <?php echo htmlspecialchars($order['created_at'] ?? 'N/A'); ?>
            </div>
        </div>
        <div class="section">
            <h3>Customer Information</h3>
            <div class="detail">
                <span class="label">Name:</span> <?php echo htmlspecialchars($order['user_name'] ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">Email:</span> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">Phone:</span> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?>
            </div>
        </div>
        <div class="section">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>रु<?php echo htmlspecialchars($item['price']); ?></td>
                        <td>रु<?php echo htmlspecialchars($item['quantity'] * $item['price']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>