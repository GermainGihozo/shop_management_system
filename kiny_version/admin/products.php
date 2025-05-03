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
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    @media (max-width: 768px) {
      .table th, .table td {
        font-size: 14px;
      }
      .card-header {
        font-size: 16px;
      }
      .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

<?php
$low_stock = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 5")->fetch(PDO::FETCH_ASSOC)['count'];
if ($low_stock > 0): ?>
  <div class="alert alert-warning text-center">
    ⚠️ <?= $low_stock ?> product(s) are low in stock!
  </div>
<?php endif; ?>

<div class="container mt-4">
  <h4 class="mb-4">📦 All Products by Branch</h4>

  <?php
  $grand_total_cost = 0; // For all branches
  foreach ($branches as $branch): ?>
    <div class="card mb-4">
      <div class="card-header bg-dark text-white">
        Branch: <?= htmlspecialchars($branch['name']) ?>
      </div>
      <div class="card-body">
        <div class="table-responsive">
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

                $total_cost = 0;

                foreach ($products as $product):
                  $product_cost = $product['price'] * $product['quantity'];
                  $total_cost += $product_cost;
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
        </div>

        <?php if (empty($products)): ?>
          <p class="text-muted">No products in this branch.</p>
        <?php else: ?>
          <div class="text-end fw-bold">
            Total Stock Cost for <?= htmlspecialchars($branch['name']) ?>:
            <span class="text-success"><?= number_format($total_cost, 0) ?> RWF</span>
          </div>
        <?php endif;

        $grand_total_cost += $total_cost;
        ?>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Grand Total -->
  <div class="card text-end mb-5">
    <div class="card-body bg-light">
      <h5 class="fw-bold">💰 Grand Total Stock Cost (All Branches): 
        <span class="text-primary"><?= number_format($grand_total_cost, 0) ?> RWF</span>
      </h5>
    </div>
  </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
