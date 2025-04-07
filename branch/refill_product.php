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

// Check if the product belongs to this branch
$check = $conn->prepare("SELECT * FROM products WHERE id = ? AND branch_id = ?");
$check->bind_param("ii", $product_id, $branch_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows !== 1) {
    die("Product not found or not accessible.");
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $add_qty = intval($_POST['quantity']);
    $update = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
    $update->bind_param("ii", $add_qty, $product_id);
    $update->execute();
    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Refill Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h4>Refill Product: <?= htmlspecialchars($product['name']) ?></h4>
  <form method="POST">
    <div class="mb-3">
      <label>Quantity to Add</label>
      <input type="number" name="quantity" class="form-control" required>
    </div>
    <button class="btn btn-success">Refill</button>
  </form>
</div>
</body>
</html>
