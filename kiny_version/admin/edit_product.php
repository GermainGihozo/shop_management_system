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
<html>
<head>
  <title>Edit Product</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
  html, body {
    height: 100%;
  }
  body {
    display: flex;
    flex-direction: column;
  }
  .container {
    flex: 1;
  }
</style>

</head>
<body>
  <?php
  include'navbar.php';
  ?>
<div class="container mt-5">
  <h4>Edit Product</h4>
  <form method="POST">
    <div class="mb-2">
      <label>Product Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Price (RWF)</label>
      <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Quantity</label>
      <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" required>
    </div>
    <button type="submit" class="btn btn-success">Save Changes</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
