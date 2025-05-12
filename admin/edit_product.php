<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

if (!isset($_GET['id'])) {
  die("Invalid Product ID.");
}

$id = $_GET['id'];

// Fetch product info
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("Product not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $new_quantity = $_POST['quantity'];

  // Update name and price directly
  $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
  $stmt->execute([$name, $price, $id]);

  // If quantity has changed, request approval
  if ($new_quantity != $product['quantity']) {
    $branch_id = $product['branch_id'];
    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO product_update_requests 
      (product_id, branch_id, requested_by_admin_id, new_quantity) 
      VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $branch_id, $admin_id, $new_quantity]);
    
    $msg = "Product updated (quantity change pending branch approval)";
  } else {
    $msg = "Product updated successfully";
  }

  header("Location: edit_product.php?msg=" . urlencode($msg));
  exit;
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
