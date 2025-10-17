<?php
require '../includes/db.php';

// Get product ID from URL
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = $_GET['id'];

// Fetch product
$stmt = $conn->prepare("SELECT * FROM online_products WHERE id = ?");
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php
include'admin_navbar.php';
?>
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4>Edit Product - <?php echo htmlspecialchars($product['name']); ?></h4>
    </div>
    <div class="card-body">
      <form action="product_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price (RWF)</label>
          <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Discount (%)</label>
          <input type="number" name="discount" class="form-control" value="<?php echo $product['discount']; ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Product Image</label><br>
          <img src="uploads/<?php echo $product['image']; ?>" width="120" class="mb-2 rounded">
          <input type="file" name="image" class="form-control">
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success">Update</button>
          <a href="products.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
 <?php
    include '../includes/footer.php';
    
    ?>
</body>
</html>
