<?php
require '../includes/db.php';

// Fetch all products
$stmt = $conn->query("SELECT * FROM online_products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f7f9fc;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 30px;
      margin-bottom: 20px;
    }
    .btn-add {
      background-color: #4b0082;
      color: white;
      border: none;
    }
    .btn-add:hover {
      background-color: #6a5acd;
    }
    table img {
      border-radius: 8px;
      width: 70px;
      height: 70px;
      object-fit: cover;
    }
    .badge-new {
      background: #28a745;
      color: white;
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 0.8rem;
    }
  </style>
</head>
<body>
    <?php
require 'admin_navbar.php';
?>
<div class="container">
  <div class="header">
    <h3 class="fw-bold text-primary">Manage Products</h3>
    <a href="admin_add_product.php" class="btn btn-add">+ Add New Product</a>
  </div>

  <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">✅ Product updated successfully!</div>
  <?php endif; ?>

  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-danger">🗑️ Product deleted successfully!</div>
  <?php endif; ?>

  <div class="card shadow">
    <div class="card-body">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Added On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($products) > 0): ?>
            <?php foreach ($products as $index => $product): ?>
              <tr>
                <td><?php echo $index + 1; ?></td>
                <td><img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product"></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>RWF <?php echo number_format($product['price']); ?></td>
                <td>
                  <?php echo $product['discount'] ? $product['discount'] . '%' : '—'; ?>
                </td>
                <td><?php echo date("M d, Y", strtotime($product['created_at'])); ?></td>
                <td>
                  <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                  <a href="product_delete.php?id=<?php echo $product['id']; ?>" 
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">No products found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
