<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');
require 'navbar.php';

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$selected_branch = $_GET['branch_id'] ?? 'all';

// Fetch branches for dropdown
$branches = $conn->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT b.name AS branch_name, DATE(s.sold_at) AS sale_day,
               SUM(s.total_price) AS total_sales,
               SUM(s.quantity) AS total_quantity
        FROM sales s
        JOIN branches b ON s.branch_id = b.id
        WHERE DATE(s.sold_at) BETWEEN :start_date AND :end_date";

if ($selected_branch !== 'all') {
    $sql .= " AND s.branch_id = :branch_id";
}

$sql .= " GROUP BY s.branch_id, DATE(s.sold_at)
          ORDER BY sale_day DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);

if ($selected_branch !== 'all') {
    $stmt->bindParam(':branch_id', $selected_branch, PDO::PARAM_INT);
}

$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    footer {
        margin-top: auto;
    }
</style>

</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Daily Sales Report by Branch</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
        </div>
        <div class="col-md-3">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
        </div>
        <div class="col-md-3">
            <label>Branch</label>
            <select name="branch_id" class="form-control">
                <option value="all" <?= $selected_branch === 'all' ? 'selected' : '' ?>>All Branches</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?= $branch['id'] ?>" <?= $selected_branch == $branch['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($branch['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="export_sales_csv.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&branch_id=<?= $selected_branch ?>" class="btn btn-success">📥 Download CSV</a>
        <a href="export_sales_pdf.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&branch_id=<?= $selected_branch ?>" class="btn btn-danger">📄 Download PDF</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Branch</th>
            <th>Sale Date</th>
            <th>Total Quantity Sold</th>
            <th>Total Sales Amount (RWF)</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($sales): ?>
            <?php 
            $count = 1; 
            $grand_total = 0;
            $labels = [];
            $chartData = [];
            foreach ($sales as $row): 
                $grand_total += $row['total_sales'];
                $labels[] = $row['branch_name'] . ' (' . $row['sale_day'] . ')';
                $chartData[] = $row['total_sales'];
            ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['branch_name']) ?></td>
                    <td><?= $row['sale_day'] ?></td>
                    <td><?= $row['total_quantity'] ?></td>
                    <td><?= number_format($row['total_sales'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No sales found for this period.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($sales)): ?>
    <div class="alert alert-info">
        <strong>Total Revenue:</strong> RWF <?= number_format($grand_total, 2) ?>
    </div>

    <canvas id="salesChart" height="100"></canvas>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Total Sales (RWF)',
                    data: <?= json_encode($chartData) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Sales Breakdown by Branch and Date'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="js/bootstrap.bundle.min.js"></script>
<?php
include'../includes/footer.php';
?>
</body>
</html>
