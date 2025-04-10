<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$products = $conn->prepare("SELECT * FROM products WHERE branch_id = ?");
$products->bind_param("i", $branch_id);
$products->execute();
$result = $products->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>📦 Products - <?= htmlspecialchars($_SESSION['name']) ?>’s Branch</h4>
  <a href="add_product.php" class="btn btn-primary mb-3">➕ Add Product</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Price (RWF)</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= number_format($row['price'], 2) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td>
            <?php if ($row['quantity'] <= $row['low_stock_threshold']): ?>
              <span class="badge bg-danger">Low Stock</span>
            <?php else: ?>
              <span class="badge bg-success">OK</span>
            <?php endif; ?>
          </td>
          <td>
            <a href="refill_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Refill</a>
            <a href="sell_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Sell</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
