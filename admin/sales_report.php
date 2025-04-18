<?php
session_start();
include '../includes/db_connection.php';

// Check if the user is logged in and is a branch user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header('Location: ../login.php');
    exit();
}

// Get the branch_id from session
if (!isset($_SESSION['branch_id'])) {
    echo "Branch ID is not set in session.";
    exit();
}

$branch_id = $_SESSION['branch_id'];

// Get filtered date range
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Fetch sales data for this branch and date range
$sql = "SELECT s.*, p.name AS product_name
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE s.branch_id = ? AND DATE(s.sale_date) BETWEEN ? AND ?
        ORDER BY s.sale_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iss', $branch_id, $start_date, $end_date);
$stmt->execute();
$sales = $stmt->get_result();

$total_sales = 0;
$total_items = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Sales Report</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
        </div>
        <div class="col-md-4">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Quantity Sold</th>
            <th>Total Amount</th>
            <th>Sale Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($sales->num_rows > 0): ?>
            <?php $count = 1; while ($row = $sales->fetch_assoc()): ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= $row['sale_date'] ?></td>
                </tr>
                <?php
                $total_sales += $row['total_amount'];
                $total_items += $row['quantity'];
                ?>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No sales found in this period.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="alert alert-info">
        <strong>Total Items Sold:</strong> <?= $total_items ?><br>
        <strong>Total Sales Amount:</strong> RWF <?= number_format($total_sales, 2) ?>
    </div>
</div>
</body>
</html>
