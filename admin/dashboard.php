<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';
require_once 'navbar.php';
$current_page = 'dashboard.php'; // <-- Make sure this is set before the navbar
requireRole('admin');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get data (same as before)
$stmt = $conn->query("SELECT SUM(total_price) AS total_sales FROM sales");
$totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

$stmt = $conn->query("SELECT COUNT(*) AS total_products FROM products");
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'] ?? 0;

$stmt = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;

// Count pending products
$stmt = $conn->query("SELECT COUNT(*) AS pending_count FROM products WHERE status = 'pending'");
$pendingCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'] ?? 0;

// Count rejected products
$stmt = $conn->query("SELECT COUNT(*) AS rejected_count FROM products WHERE status = 'rejected'");
$rejectedCount = $stmt->fetch(PDO::FETCH_ASSOC)['rejected_count'] ?? 0;


$stmt = $conn->query("SELECT 
    b.name AS branch_name,
    SUM(s.total_price) AS branch_sales,
    SUM(s.quantity) AS products_sold,
    MAX(s.sold_at) AS last_updated
FROM sales s
JOIN branches b ON s.branch_id = b.id
GROUP BY s.branch_id");
$branchSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$branchNames = array_column($branchSales, 'branch_name');
$branchSalesData = array_column($branchSales, 'branch_sales');
$productsSoldData = array_column($branchSales, 'products_sold');
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { overflow-x: hidden; display: flex; flex-direction: column; min-height: 100vh; }
    .content { margin-left: 0; padding: 20px; flex: 1; } /* Content will be flexible */
    footer { margin-top: auto; }

    @media (max-width: 991px) {
      .content { margin-left: 0; }
    }
  </style>
</head>
<body>

<div class="content">
  <div class="container-fluid">
    <h2 class="mb-4">👑 Admin Dashboard</h2>

    <!-- Key Stats -->
    <div class="row g-3 mb-4">
      <!-- Use col-md-4 for large screens and col-12 for full width on smaller screens -->
      <div class="col-md-4 col-12">
        <div class="card bg-primary text-white shadow p-3 animate__animated animate__fadeIn">
          <h5><i class="bi bi-cash-stack"></i> Total Sales (RWF)</h5>
          <h3><?php echo number_format($totalSales); ?></h3>
        </div>
      </div>
      
      <div class="col-md-4 col-12">
        <div class="card bg-success text-white shadow p-3 animate__animated animate__fadeIn">
          <h5><i class="bi bi-box-seam"></i> Total Products</h5>
          <h3><?php echo $totalProducts; ?></h3>
        </div>
      </div>
      <div class="col-md-4 col-12">
        <div class="card bg-warning text-dark shadow p-3 animate__animated animate__fadeIn">
          <h5><i class="bi bi-people"></i> Total Users</h5>
          <h3><?php echo $totalUsers; ?></h3>
        </div>
      </div>
      <div class="col-md-6 col-lg-6 col-xl-4 col-12">
    <div class="card bg-danger text-white shadow p-3 animate__animated animate__fadeIn">
        <h5><i class="bi bi-x-circle"></i> Rejected Products</h5>
        <h3><?= $rejectedCount ?></h3>
    </div>
</div>

<div class="col-md-6 col-lg-6 col-xl-4 col-12">
    <div class="card bg-info text-white shadow p-3 animate__animated animate__fadeIn">
        <h5><i class="bi bi-hourglass-split"></i> Pending Approvals</h5>
        <h3><?= $pendingCount ?></h3>
    </div>
</div>

    </div>

    <!-- Branch Sales Summary -->
    <div class="row">
      <div class="col-12">
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
                <td><?= htmlspecialchars($row['branch_name']) ?></td>
                <td>RWF <?= number_format($row['branch_sales']) ?></td>
                <td><?= $row['products_sold'] ?></td>
                <td><?= $row['last_updated'] ? date('Y-m-d H:i', strtotime($row['last_updated'])) : 'N/A' ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($branchSales)): ?>
              <tr><td colspan="4">No data available.</td></tr>
              <?php else: ?>
              <tr class="table-secondary fw-bold">
                <td>Total</td>
                <td>RWF <?= number_format(array_sum($branchSalesData)) ?></td>
                <td><?= array_sum($productsSoldData) ?></td>
                <td>-</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Sales and Products Charts -->
    <div class="row">
      <div class="col-md-6 col-12">
        <div class="card shadow p-3">
          <h5>📊 Sales by Branch</h5>
          <canvas id="salesChart"></canvas>
        </div>
      </div>
      <div class="col-md-6 col-12">
        <div class="card shadow p-3">
          <h5>📈 Products Sold by Branch</h5>
          <canvas id="productsChart"></canvas>
        </div>
      </div>
      
    </div>

  </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
  // Sales Chart
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(salesCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($branchNames); ?>,
      datasets: [{
        label: 'Total Sales (RWF)',
        data: <?php echo json_encode($branchSalesData); ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.6)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Products Sold Chart
  const productsCtx = document.getElementById('productsChart').getContext('2d');
  const productsChart = new Chart(productsCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($branchNames); ?>,
      datasets: [{
        label: 'Products Sold',
        data: <?php echo json_encode($productsSoldData); ?>,
        backgroundColor: 'rgba(255, 159, 64, 0.6)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<?php include '../includes/footer.php'; ?>

</body>
</html>
