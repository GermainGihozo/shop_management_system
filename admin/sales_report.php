<?php
session_start();
require '../includes/db.php'; // Assumes $conn is a valid PDO instance
require '../includes/auth.php';
requireRole('admin');
require'navbar.php';

// Use a fixed or session-based branch_id for now
$branch_id = $_SESSION['branch_id'] ?? 1; // fallback to branch ID 1 if not set

// Date filters (defaults to current month)
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// SQL query with named placeholders and branch join
$sql = "SELECT s.*, p.name AS product_name, b.name AS branch_name
        FROM sales s
        JOIN products p ON s.product_id = p.id
        JOIN branches b ON s.branch_id = b.id
        WHERE s.branch_id = :branch_id
        AND DATE(s.sold_at) BETWEEN :start_date AND :end_date
        ORDER BY s.sold_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':branch_id', $branch_id);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_sales = 0;
$total_items = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">All Branches Sales Report</h2>

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

    <div class="mb-3">
        <a href="export_sales_csv.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-success">📥 Download CSV</a>
        <a href="export_sales_pdf.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-danger">📄 Download PDF</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Branch</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total Amount (RWF)</th>
            <th>Sale Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($sales): ?>
            <?php $count = 1; foreach ($sales as $row): ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['branch_name']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['sold_at']) ?></td>
                </tr>
                <?php
                $total_sales += $row['total_amount'];
                $total_items += $row['quantity'];
                ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No sales found in this period.</td></tr>
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
