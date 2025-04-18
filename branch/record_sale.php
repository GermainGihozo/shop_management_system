<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$message = "";

// Get all products for the branch
$stmt = $conn->prepare("SELECT id, name, quantity, price FROM products WHERE branch_id = ?");
$stmt->execute([$branch_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle sale form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity_sold = intval($_POST['quantity']);

    // Fetch selected product details
    $stmt = $conn->prepare("SELECT quantity, price FROM products WHERE id = ? AND branch_id = ?");
    $stmt->execute([$product_id, $branch_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if ($product['quantity'] >= $quantity_sold && $quantity_sold > 0) {
            $price_each = $product['price'];
            $total_price = $price_each * $quantity_sold;

            // Update stock
            $update = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $update->execute([$quantity_sold, $product_id]);

            // Record sale
            $insert = $conn->prepare("INSERT INTO sales (product_id, branch_id, quantity, price_each, total_price, sold_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $insert->execute([$product_id, $branch_id, $quantity_sold, $price_each, $total_price]);

            $message = "<div class='alert alert-success'>✅ Sale recorded successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Not enough stock or invalid quantity.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>⚠️ Product not found!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Record Sale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h4>🧾 Record Product Sale</h4>
  <?= $message ?>

  <form method="POST" class="p-4 bg-white rounded shadow-sm mt-4">
    <div class="mb-3">
      <label>Select Product</label>
      <select name="product_id" class="form-select" required>
        <option value="">-- Choose Product --</option>
        <?php foreach ($products as $product): ?>
          <option value="<?= $product['id'] ?>">
            <?= htmlspecialchars($product['name']) ?> (Stock: <?= $product['quantity'] ?> | RWF <?= number_format($product['price'], 2) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Quantity Sold</label>
      <input type="number" name="quantity" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
      <label>Date of Sale</label>
      <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s') ?>" readonly>
    </div>

    <button type="submit" class="btn btn-success">💾 Record Sale</button>
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
  </form>
</div>
</body>
</html>
