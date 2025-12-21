<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('branch');

$branch_id = $_SESSION['branch_id'];

// Fetch products for current branch
$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE branch_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$branch_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Branch Stock</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    .badge-status {
        font-size: 0.8rem;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">

  <h4 class="mb-3">📦 Branch Products</h4>

  <div class="card shadow-sm">
    <div class="card-body">

      <?php if (empty($products)): ?>
        <div class="alert alert-info mb-0">
          No products added yet.
        </div>
      <?php else: ?>

      <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>Product</th>
            <th>Price (RWF)</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($products as $product): ?>
            <tr>
              <td><?= htmlspecialchars($product['name']) ?></td>
              <td><?= number_format($product['price'], 2) ?></td>
              <td><?= $product['quantity'] ?></td>
              <td>
                <?php if ($product['status'] === 'approved'): ?>
                  <span class="badge bg-success badge-status">Approved</span>
                <?php elseif ($product['status'] === 'pending'): ?>
                  <span class="badge bg-warning text-dark badge-status">Pending</span>
                <?php else: ?>
                  <span class="badge bg-danger badge-status">Rejected</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($product['status'] === 'rejected'): ?>
                  <small class="text-danger">
                    <?= htmlspecialchars($product['rejection_reason'] ?? 'No reason provided') ?>
                  </small>
                <?php elseif ($product['status'] === 'pending'): ?>
                  <small class="text-muted">Awaiting admin approval</small>
                <?php else: ?>
                  <small class="text-success">Ready for sale</small>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>
      </div>

      <?php endif; ?>

    </div>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
