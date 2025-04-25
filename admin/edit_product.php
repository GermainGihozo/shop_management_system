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

  $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
  $stmt->execute([$name, $price, $id]);

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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>Edit Product</h4>
  <form method="POST">
    <div class="mb-3">
      <label>Product Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Price (RWF)</label>
      <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
    </div>
    <button type="submit" class="btn btn-success">Save Changes</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<?php
include'../includes/footer.php';
?>
</body>
</html>
