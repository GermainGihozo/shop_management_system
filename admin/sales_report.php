<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';
require '../includes/auth.php';
requireRole('branch');

// ✅ Ensure branch_id is set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['branch_id'])) {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];

// Date and product filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$product_id = $_GET['product'] ?? 'all';

$query = "SELECT sales.*, products.name AS product_name 
          FROM sales 
          JOIN products ON sales.product_id = products.id 
          WHERE sales.branch_id = :branch_id";

$params = ['branch_id' => $branch_id];

if (!empty($from) && !empty($to)) {
    $query .= " AND DATE(sold_at) BETWEEN :from AND :to";
    $params['from'] = $from;
    $params['to'] = $to;
}

if ($product_id !== 'all') {
    $query .= " AND sales.product_id = :product_id";
    $params['product_id'] = $product_id;
}

$query .= " ORDER BY sold_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products for dropdown
$products = $conn->prepare("SELECT id, name FROM products WHERE branch_id = ?");
$products->execute([$branch_id]);
$productList = $products->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_sales = 0;
$total_transactions = count($sales);
foreach ($sales as $s) {
    $total_sales += $s['total_price'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h4>📋 Sales Report</h4>

    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-3">
            <label>Date From</label>
            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-3">
            <label>Date To</label>
            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-3">
            <label>Product</label>
            <select name="product" class="form-select">
                <option value="all">All Products</option>
                <?php foreach ($productList as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($product_id == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">🔍 Filter</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="export_sales_pdf.php?from=<?= $from ?>&to=<?= $to ?>&product=<?= $product_id ?>" class="btn btn-danger">
            📄 Export PDF
        </a>
        <a href="export_sales_csv.php?from=<?= $from ?>&to=<?= $to ?>&product=<?= $product_id ?>" class="btn btn-success">
            📥 Export CSV
        </a>
    </div>

    <div class="alert alert-info">
        <strong>Total Sales:</strong> RWF <?= number_format($total_sales, 2) ?> |
        <strong>Total Transactions:</strong> <?= $total_transactions ?>
    </div>

    <?php if (count($sales) === 0): ?>
        <div class="alert alert-warning">No sales found for selected filters.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price Each (RWF)</th>
                    <th>Total (RWF)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['product_name']) ?></td>
                        <td><?= $s['quantity'] ?></td>
                        <td><?= number_format($s['price_each'], 2) ?></td>
                        <td><?= number_format($s['total_price'], 2) ?></td>
                        <td><?= $s['sold_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
