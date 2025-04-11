<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if ($name && $price > 0 && $quantity >= 0) {
        // Check if product with same name already exists for this branch
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ? AND branch_id = ?");
        $stmt->execute([$name, $branch_id]);

        if ($stmt->rowCount() > 0) {
            $message = "<div class='alert alert-warning'>🚫 Product already exists!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, branch_id) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $price, $quantity, $branch_id])) {
                $message = "<div class='alert alert-success'>✅ Product added successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Failed to add product. Try again.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>⚠️ Please fill all fields correctly.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h4>➕ Add New Product</h4>
  <?= $message ?>

  <form method="POST" class="mt-4 p-4 bg-white rounded shadow-sm">
    <div class="mb-3">
      <label>Product Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Price (RWF)</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Quantity</label>
      <input type="number" name="quantity" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Save Product</button>
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
  </form>
</div>
</body>
</html>
