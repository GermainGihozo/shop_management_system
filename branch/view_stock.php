<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('branch');

$branch_id = $_SESSION['branch_id'];
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $qty = $_POST['quantity'];

    // Check if product already exists for this branch
    $check = $conn->prepare("SELECT id FROM products WHERE name = ? AND branch_id = ?");
    $check->execute([$name, $branch_id]);

    if ($check->fetch()) {
        $error = "⚠️ Product '$name' already exists in your branch.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, branch_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $qty, $branch_id]);
        header("Location: view_stock.php"); // refresh to prevent resubmission
        exit;
    }
}

// Fetch products for current branch
$stmt = $conn->prepare("SELECT * FROM products WHERE branch_id = ?");
$stmt->execute([$branch_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Branch Products</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>🛒 Add New Product</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3 mb-5">
    <div class="col-md-4">
      <input type="text" name="name" class="form-control" placeholder="Product Name" required>
    </div>
    <div class="col-md-3">
      <input type="number" name="price" class="form-control" placeholder="Price (RWF)" required>
    </div>
    <div class="col-md-3">
      <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Add Product</button>
    </div>
  </form>

  <h4>📦 Current Products</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>Price (RWF)</th>
        <th>Stock</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= number_format($product['price'], 2) ?></td>
          <td><?= $product['quantity'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
