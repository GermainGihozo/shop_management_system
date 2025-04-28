<?php
session_start();
require 'navbar.php';
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'] ?? 0;

// Total products in stock
$product_stmt = $conn->prepare("SELECT SUM(quantity) as total_stock FROM products WHERE branch_id = ?");
$product_stmt->execute([$branch_id]);
$total_stock = $product_stmt->fetchColumn() ?? 0;

// Today's sales total
$sales_stmt = $conn->prepare("SELECT SUM(total_price) as total_sales FROM sales WHERE branch_id = ? AND DATE(sold_at) = CURDATE()");
$sales_stmt->execute([$branch_id]);
$today_sales = $sales_stmt->fetchColumn() ?? 0;

// Products sold today
$sold_stmt = $conn->prepare("SELECT SUM(quantity) as total_sold FROM sales WHERE branch_id = ? AND DATE(sold_at) = CURDATE()");
$sold_stmt->execute([$branch_id]);
$today_sold_qty = $sold_stmt->fetchColumn() ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Branch Dashboard</title>
  
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3 class="mb-4">Hello <?= htmlspecialchars($_SESSION['username']) ?> 👋</h3>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h5 class="card-title">📦 Total Stock</h5>
          <p class="card-text fs-4"><?= $total_stock ?> items</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <h5 class="card-title">💰 Today’s Sales</h5>
          <p class="card-text fs-4"><?= number_format($today_sales, 2) ?> RWF</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <h5 class="card-title">🛒 Products Sold Today</h5>
          <p class="card-text fs-4"><?= $today_sold_qty ?> items</p>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h5>Quick Links</h5>
    <div class="d-flex flex-wrap gap-3">
      <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> ➕ Add Product</a>
      <a href="record_sale.php" class="btn btn-primary">💸 Record Sale</a>
      <a href="view_products.php" class="btn btn-info">👀 View Products</a>
      <a href="sales_report.php" class="btn btn-secondary">📋 Sales Report</a>
    </div>
  </div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
