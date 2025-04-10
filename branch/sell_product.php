<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$product_id = intval($_GET['id']);

$check = $conn->prepare("SELECT * FROM products WHERE id = ? AND branch_id = ?");
$check->bind_param("ii", $product_id, $branch_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows !== 1) {
    die("Product not found or not accessible.");
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sold_qty = intval($_POST['quantity']);

    if ($sold_qty > $product['quantity']) {
        die("Cannot sell more than available quantity.");
    }

    $new_quantity = $product['quantity'] - $sold_qty;
    $price_each = $product['price'];
    $total_price = $sold_qty * $price_each;

    // 1. Update stock
    $update = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $new_quantity, $product_id);
    $update->execute();

    // 2. Insert sale
    $insert = $conn->prepare("INSERT INTO sales (product_id, branch_id, quantity, price_each, total_price) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("iiidd", $product_id, $branch_id, $sold_qty, $price_each, $total_price);
    $insert->execute();

    header("Location: products.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Sell Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>Sell Product: <?= htmlspecialchars($product['name']) ?></h4>
  <form method="POST">
    <div class="mb-3">
      <label>Quantity Sold (Available: <?= $product['quantity'] ?>)</label>
      <input type="number" name="quantity" class="form-control" required max="<?= $product['quantity'] ?>">
    </div>
    <button class="btn btn-danger">Sell</button>
  </form>
</div>
</body>
</html>
