<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';
require '../includes/auth.php';
requireRole('admin'); // or 'branch'

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get filter values
$filter = $_GET['filter'] ?? 'daily';
$branch_filter = $_GET['branch'] ?? 'all';

// Build dynamic query
$date_filter = "";
switch ($filter) {
    case 'weekly':
        $date_filter = "AND YEARWEEK(sold_at) = YEARWEEK(CURDATE())";
        break;
    case 'monthly':
        $date_filter = "AND MONTH(sold_at) = MONTH(CURDATE()) AND YEAR(sold_at) = YEAR(CURDATE())";
        break;
    default: // daily
        $date_filter = "AND DATE(sold_at) = CURDATE()";
        break;
}

$query = "SELECT sales.*, products.name AS product_name, branches.name AS branch_name 
          FROM sales 
          JOIN products ON sales.product_id = products.id 
          JOIN branches ON sales.branch_id = branches.id 
          WHERE 1=1 ";

$params = [];

// Add branch filter if set
if ($branch_filter !== 'all') {
    $query .= " AND sales.branch_id = ? ";
    $params[] = $branch_filter;
}

// Add date filter
$query .= " $date_filter ORDER BY sales.sold_at DESC";

$stmt = $conn->prepare($query);
foreach ($params as $index => $param) {
    $stmt->bindValue($index + 1, $param, PDO::PARAM_INT);
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all branches for dropdown
$branches = $conn->query("SELECT id, name FROM branches");

$total_sales = 0;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Sales Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>📋 All Branches Sales Report</h4>

  <form method="GET" class="row mb-3">
    <div class="col-md-4">
      <label>Filter By Branch</label>
      <select name="branch" class="form-select">
        <option value="all">All Branches</option>
        <?php while ($b = $branches->fetch(PDO::FETCH_ASSOC)): ?>
          <option value="<?= $b['id'] ?>" <?= ($branch_filter == $b['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($b['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label>Time Filter</label>
      <select name="filter" class="form-select">
        <option value="daily" <?= ($filter === 'daily') ? 'selected' : '' ?>>Today</option>
        <option value="weekly" <?= ($filter === 'weekly') ? 'selected' : '' ?>>This Week</option>
        <option value="monthly" <?= ($filter === 'monthly') ? 'selected' : '' ?>>This Month</option>
      </select>
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button class="btn btn-primary w-100">Apply</button>
    </div>
    <a href="export_sales_csv.php?filter=<?= $filter ?>&branch=<?= $branch_filter ?>" class="btn btn-success mb-3">
      📥 Download CSV
    </a>
    <a href="export_sales_pdf.php?filter=<?= $filter ?>&branch=<?= $branch_filter ?>" class="btn btn-danger mb-3">
      📄 Download PDF
    </a>
  </form>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Branch</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Price Each (RWF)</th>
        <th>Total (RWF)</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result as $row): 
          $total_sales += $row['total_price'];
      ?>
        <tr>
          <td><?= htmlspecialchars($row['branch_name']) ?></td>
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= number_format($row['price_each'], 2) ?></td>
          <td><?= number_format($row['total_price'], 2) ?></td>
          <td><?= $row['sold_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="4" class="text-end">Total Sales</th>
        <th colspan="2"><?= number_format($total_sales, 2) ?> RWF</th>
      </tr>
    </tfoot>
  </table>
</div>
</body>
</html>
