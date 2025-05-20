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
  $quantity_to_add = (int)$_POST['quantity_to_add'];

  // Update name and price directly
  $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
  $stmt->execute([$name, $price, $id]);

  if ($quantity_to_add > 0) {
    $branch_id = $product['branch_id'];
    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO product_update_requests 
      (product_id, branch_id, requested_by_admin_id, added_quantity) 
      VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $branch_id, $admin_id, $quantity_to_add]);

    $msg = "Refill request submitted (pending branch approval)";
  } else {
    $msg = "Product info updated (no quantity refill)";
  }

  header("Location: edit_product.php?id=$id&msg=" . urlencode($msg));
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <h4>Edit Product</h4>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

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
    <div class="mb-3">
  <label>Quantity to Add (Refill)</label>
  <input type="number" name="quantity_to_add" class="form-control" placeholder="e.g. 10" min="1">
</div>
    <button type="submit" class="btn btn-success">Save Changes</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
