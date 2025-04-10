<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('admin');

// Fetch all branches for the dropdown
$branches = $conn->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Branch filter logic
$branchId = isset($_GET['branch']) ? $_GET['branch'] : null;

if ($branchId) {
    $stmt = $conn->prepare("SELECT p.*, b.name AS branch_name 
                            FROM products p
                            JOIN branches b ON p.branch_id = b.id
                            WHERE branch_id = ?");
    $stmt->execute([$branchId]);
} else {
    $stmt = $conn->query("SELECT p.*, b.name AS branch_name 
                          FROM products p
                          JOIN branches b ON p.branch_id = b.id");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4 class="mb-4">🛍️ Product Management</h4>

  <!-- Branch Filter -->
  <form method="GET" class="mb-4">
    <label for="branch" class="form-label">Filter by Branch:</label>
    <select name="branch" id="branch" class="form-select w-auto d-inline-block">
      <option value="">All Branches</option>
      <?php foreach ($branches as $branch): ?>
        <option value="<?= $branch['id'] ?>" <?= $branchId == $branch['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($branch['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-primary ms-2">Filter</button>
  </form>

  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Name</th>
        <th>Price (RWF)</th>
        <th>Quantity</th>
        <th>Branch</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product): ?>
        <tr class="<?= $product['quantity'] < 5 ? 'table-warning' : '' ?>">
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= number_format($product['price']) ?></td>
          <td><?= $product['quantity'] ?></td>
          <td><?= htmlspecialchars($product['branch_name']) ?></td>
          <td><?= $product['quantity'] < 5 ? '⚠️ Low Stock' : '✔️ OK' ?></td>
          <td><a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
