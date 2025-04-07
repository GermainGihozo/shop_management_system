<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $low_stock_threshold = intval($_POST['low_stock_threshold']);
    $branch_id = $_SESSION['branch_id'];

    $stmt = $conn->prepare("INSERT INTO products (branch_id, name, price, quantity, low_stock_threshold) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdii", $branch_id, $name, $price, $quantity, $low_stock_threshold);
    $stmt->execute();

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h4>Add Product</h4>
  <form method="POST">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Price (RWF)</label>
      <input type="number" name="price" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Initial Quantity</label>
      <input type="number" name="quantity" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Low Stock Alert At</label>
      <input type="number" name="low_stock_threshold" class="form-control" value="5" required>
    </div>
    <button class="btn btn-primary">Save</button>
  </form>
</div>
</body>
</html>
