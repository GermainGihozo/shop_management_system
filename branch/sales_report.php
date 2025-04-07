<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$filter = $_GET['filter'] ?? 'daily';

switch ($filter) {
    case 'weekly':
        $query = "SELECT * FROM sales WHERE branch_id = ? AND YEARWEEK(sold_at) = YEARWEEK(CURDATE())";
        break;
    case 'monthly':
        $query = "SELECT * FROM sales WHERE branch_id = ? AND MONTH(sold_at) = MONTH(CURDATE()) AND YEAR(sold_at) = YEAR(CURDATE())";
        break;
    default: // daily
        $query = "SELECT * FROM sales WHERE branch_id = ? AND DATE(sold_at) = CURDATE()";
        break;
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sales Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h4>🧾 <?= ucfirst($filter) ?> Sales Report</h4>
  <div class="mb-3">
    <a href="?filter=daily" class="btn btn-outline-primary">Today</a>
    <a href="?filter=weekly" class="btn btn-outline-primary">This Week</a>
    <a href="?filter=monthly" class="btn btn-outline-primary">This Month</a>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Product ID</th>
        <th>Qty Sold</th>
        <th>Price Each (RWF)</th>
        <th>Total (RWF)</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($sale = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $sale['product_id'] ?></td>
          <td><?= $sale['quantity'] ?></td>
          <td><?= number_format($sale['price_each'], 2) ?></td>
          <td><?= number_format($sale['total_price'], 2) ?></td>
          <td><?= $sale['sold_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
