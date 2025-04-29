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
            $message = "<div class='alert alert-warning'>🚫 Igicuruzwa gisanzwe kirimo!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, branch_id) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $price, $quantity, $branch_id])) {
                $message = "<div class='alert alert-success'>✅ Kwinjiza igicuruzwa byakunze!</div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Kwinjiza byanze. Ongera ugerageze.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>⚠️ Uzuza amakuru yose.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h4>➕ Injiza igicuruzwa</h4>
  <?= $message ?>

  <form method="POST" class="mt-4 p-4 bg-white rounded shadow-sm">
    <div class="mb-3">
      <label>Izina ry'igicuruzwa</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Igiciro (RWF)</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Ingano</label>
      <input type="number" name="quantity" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Emeza</button>
    <a href="dashboard.php" class="btn btn-secondary">← Subira Inyuma</a>
  </form>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
