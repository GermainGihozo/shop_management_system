<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';
require_once 'navbar.php';
$current_page = 'dashboard.php';
requireRole('admin');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get total sales
$stmt = $conn->query("SELECT SUM(total_price) AS total_sales FROM sales");
$totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;


// Get total products
$stmt = $conn->query("SELECT COUNT(*) AS total_products FROM products");
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'] ?? 0;

// Get total users
$stmt = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;

// Get branch-wise sales
$stmt = $conn->query("
    SELECT 
        b.name AS branch_name,
        SUM(s.total_price) AS branch_sales,
        SUM(s.quantity) AS products_sold,
        MAX(s.sold_at) AS last_updated
    FROM sales s
    JOIN branches b ON s.branch_id = b.id
    GROUP BY s.branch_id
");
$branchSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body { overflow-x: hidden; }
    .sidebar { position: fixed; top: 0; left: 0; height: 100vh; z-index: 1000; }
    .content { margin-left: 50px; padding: 20px; }

    @media (max-width: 991px) {
      .content { margin-left: 0; }
    }
  </style>
</head>

<div class="content">
  <div class="container-fluid">
    <h2 class="mb-4">👑 Admin Dashboard</h2>

    <!-- Key Stats -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card bg-primary text-white shadow p-3">
          <h5>Total Sales (RWF)</h5>
          <h3><?php echo number_format($totalSales); ?></h3>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white shadow p-3">
          <h5>Total Products</h5>
          <h3><?php echo $totalProducts; ?></h3>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-warning text-dark shadow p-3">
          <h5>Total Users</h5>
          <h3><?php echo $totalUsers; ?></h3>
        </div>
      </div>
    </div>

    <!-- Branch Sales Summary -->
    <div class="row">
      <div class="col-md-12">
        <div class="card shadow p-3">
          <h5>📍 Sales by Branch</h5>
          <table class="table table-bordered mt-3">
            <thead class="table-dark">
              <tr>
                <th>Branch</th>
                <th>Total Sales</th>
                <th>Products Sold</th>
                <th>Last Updated</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($branchSales as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['branch']) ?></td>
                <td>RWF <?= number_format($row['total_sales']) ?></td>
                <td><?= $row['products_sold'] ?></td>
                <td><?= $row['last_updated'] ?? 'N/A' ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($branchSales)): ?>
              <tr><td colspan="4">No data available.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
