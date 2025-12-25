<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    $stmt = $pdo->prepare("UPDATE products SET product_name = ?, description = ?, price = ?, stock_quantity = ? WHERE product_id = ?");
    $stmt->execute([$name, $description, $price, $stock, $id]);
    header('Location: products.php');
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - GroceryPlus Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Roboto', Arial, sans-serif; background: linear-gradient(135deg, #e8f5e8, #f1f8e9); margin: 0; padding: 20px; color: #333; }
        .header { background: linear-gradient(135deg, #4CAF50, #388E3C); color: white; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .header h1 { margin: 0; font-weight: 500; display: flex; align-items: center; }
        .header h1 img { width: 40px; height: 40px; margin-right: 0.5rem; }
        .back { background: #8BC34A; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 25px; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(139, 195, 74, 0.3); }
        .back:hover { background: #689f38; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(139, 195, 74, 0.4); }
        .form-container { background: white; padding: 3rem; margin: 0 auto; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); max-width: 700px; }
        form { display: flex; flex-direction: column; }
        .form-row { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; }
        .form-group { flex: 1; }
        label { display: block; margin-bottom: 0.75rem; color: #555; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 1rem; transition: all 0.3s ease; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #4CAF50; box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1); }
        textarea { resize: vertical; min-height: 120px; }
        .btn-container { display: flex; gap: 1rem; margin-top: 1rem; }
        button, .btn-secondary { flex: 1; padding: 1rem; background: linear-gradient(135deg, #4CAF50, #388E3C); color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 1rem; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        button:hover, .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4); }
        .btn-secondary { background: linear-gradient(135deg, #8BC34A, #689F38); }
        .btn-secondary:hover { background: linear-gradient(135deg, #689F38, #558b2f); }
        @media (max-width: 768px) { 
            body { padding: 15px; }
            .form-container { padding: 2rem 1.5rem; margin: 1rem; }
            .form-row { flex-direction: column; gap: 1rem; }
            .btn-container { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><img src="../images/splash_logo.png" alt="Logo">Edit Product</h1>
        <a href="products.php" class="back"><i class="fas fa-arrow-left"></i> Back to Products</a>
    </div>
    <div class="form-container">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (रु):</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category">
                        <option value="1" <?php echo ($product['category_id'] == 1) ? 'selected' : ''; ?>>Fruits</option>
                        <option value="2" <?php echo ($product['category_id'] == 2) ? 'selected' : ''; ?>>Vegetables</option>
                        <option value="3" <?php echo ($product['category_id'] == 3) ? 'selected' : ''; ?>>Dairy</option>
                        <option value="4" <?php echo ($product['category_id'] == 4) ? 'selected' : ''; ?>>Bakery</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="btn-container">
                <button type="submit"><i class="fas fa-save"></i> Update Product</button>
                <a href="products.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>