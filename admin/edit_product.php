<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

if (!isset($_GET['id'])) {
  die("Invalid Product ID.");
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $quantity = $_POST['quantity'];

  $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?");
  $stmt->execute([$name, $price, $quantity, $id]);

  header("Location: products.php?msg=Product updated");
  exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("Product not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .container {
      flex: 1;
    }
    footer {
      margin-top: auto;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="container mt-5">
    <h4 class="mb-4">Edit Product</h4>
    <form method="POST" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Price (RWF)</label>
        <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php include '../includes/footer.php'; ?>
</body>
</html>
