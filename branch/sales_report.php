<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];

// Build dynamic query based on filters
$query = "
    SELECT sales.*, products.name AS product_name
    FROM sales
    JOIN products ON sales.product_id = products.id
    WHERE sales.branch_id = ?
";
$params = [$branch_id];

if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $query .= " AND DATE(sales.sold_at) BETWEEN ? AND ?";
    $params[] = $_GET['start_date'];
    $params[] = $_GET['end_date'];
}

if (!empty($_GET['product_name'])) {
    $query .= " AND products.name = ?";
    $params[] = $_GET['product_name'];
}

$query .= " ORDER BY sales.sold_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch product list for dropdown
$product_stmt = $conn->prepare("
    SELECT DISTINCT products.name AS product_name
    FROM products
    JOIN sales ON sales.product_id = products.id
    WHERE sales.branch_id = ?
");
$product_stmt->execute([$branch_id]);
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total sales
$total = 0;
foreach ($sales as $sale) {
    $total += $sale['total_price'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sales Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
  <h4>📋 Sales Report</h4>

  <!-- Filter Form -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
      <label>Date From</label>
      <input type="date" name="start_date" class="form-control" value="<?= $_GET['start_date'] ?? '' ?>">
    </div>
    <div class="col-sm-6 col-md-3">
      <label>Date To</label>
      <input type="date" name="end_date" class="form-control" value="<?= $_GET['end_date'] ?? '' ?>">
    </div>
    <div class="col-sm-8 col-md-4">
      <label>Product</label>
      <select name="product_name" class="form-control">
        <option value="">All Products</option>
        <?php foreach ($products as $product): ?>
          <option value="<?= $product['product_name'] ?>" <?= ($_GET['product_name'] ?? '') == $product['product_name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($product['product_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-4 col-md-2 d-grid align-items-end">
      <button class="btn btn-primary">🔍 Filter</button>
    </div>
  </form>

  <!-- Export Buttons -->
  <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mb-3">
    <a href="export_sales_pdf.php" class="btn btn-danger">📄 Export PDF</a>
    <a href="export_sales_csv.php" class="btn btn-success">📁 Export CSV</a>
  </div>

  <!-- Summary -->
  <div class="alert alert-info">
    <strong>Total Sales:</strong> RWF <?= number_format($total, 2) ?> |
    <strong>Total Transactions:</strong> <?= count($sales) ?>
  </div>

  <?php if (!$sales): ?>
    <div class="alert alert-warning">No sales found for selected filters.</div>
  <?php else: ?>
    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total (RWF)</th>
            <th>Sale Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sales as $index => $sale): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($sale['product_name']) ?></td>
              <td><?= $sale['quantity'] ?></td>
              <td><?= number_format($sale['total_price'], 2) ?></td>
              <td><?= $sale['sold_at'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Chart -->
    <div class="mt-5">
      <h5>📈 Sales Trend</h5>
      <canvas id="salesChart" height="100"></canvas>
    </div>

    <script>
      const ctx = document.getElementById('salesChart').getContext('2d');
      const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_map(fn($s) => date('Y-m-d', strtotime($s['sold_at'])), $sales)) ?>,
          datasets: [{
            label: 'Sales (RWF)',
            data: <?= json_encode(array_column($sales, 'total_price')) ?>,
            backgroundColor: '#0d6efd'
          }]
        },
        options: {
          responsive: true,
          scales: {
            x: {
              title: { display: true, text: 'Date' }
            },
            y: {
              title: { display: true, text: 'RWF' },
              beginAtZero: true
            }
          }
        }
      });
    </script>
  <?php endif; ?>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
