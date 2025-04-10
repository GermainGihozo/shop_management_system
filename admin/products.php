<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('admin');

$branches = $conn->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<?php
$low_stock = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 5")->fetch(PDO::FETCH_ASSOC)['count'];
if ($low_stock > 0): ?>
  <div class="alert alert-warning">
    ⚠️ <?= $low_stock ?> product(s) are low in stock!
  </div>
<?php endif; ?>

<div class="container mt-4">
  <h4 class="mb-4">📦 All Products by Branch</h4>

  <?php foreach ($branches as $branch): ?>
    <div class="card mb-4">
      <div class="card-header bg-dark text-white">
        Branch: <?= htmlspecialchars($branch['name']) ?>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Product Name</th>
              <th>Price (RWF)</th>
              <th>Quantity</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $stmt = $conn->prepare("SELECT * FROM products WHERE branch_id = ?");
              $stmt->execute([$branch['id']]);
              $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

              foreach ($products as $product):
            ?>
              <tr class="<?= ($product['quantity'] < 5) ? 'table-warning' : '' ?>">
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= number_format($product['price'], 0) ?></td>
                <td><?= $product['quantity'] ?></td>
                <td>
                  <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php if (empty($products)): ?>
          <p class="text-muted">No products in this branch.</p>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
