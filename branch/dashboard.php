<?php
session_start();
require 'navbar.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Branch Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h3>Hello <?= htmlspecialchars($_SESSION['username']) ?> 👋</h3>
    <p class="text-muted">Welcome to your Branch Dashboard.</p>

    <div class="row mt-4">
      <div class="col-md-4 mb-3">
        <a href="products.php" class="btn btn-primary w-100 py-3">
          🛍 View/Add Products
        </a>
      </div>
      <div class="col-md-4 mb-3">
        <a href="record_sale.php" class="btn btn-success w-100 py-3">
          🧾 Record Sale
        </a>
      </div>
      <div class="col-md-4 mb-3">
        <a href="sales_summary.php" class="btn btn-warning w-100 py-3">
          📈 Daily Sales Summary
        </a>
      </div>
    </div>
  </div>
</body>
</html>
