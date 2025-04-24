<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];

// Build query with filters
$query = "
    SELECT sales.*, products.name AS product_name 
    FROM sales 
    JOIN products ON sales.product_id = products.id 
    WHERE sales.branch_id = ?
";
$params = [$branch_id];

if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $query .= " AND DATE(sales.sold_at) BETWEEN ? AND ?";
    $params[] = $_GET['start_date'];
    $params[] = $_GET['end_date'];
}

if (!empty($_GET['product_name'])) {
    $query .= " AND products.name = ?";
    $params[] = $_GET['product_name'];
}

$query .= " ORDER BY sales.sold_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=sales_report.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, ['#', 'Product', 'Quantity', 'Total (RWF)', 'Sale Date']);

// Rows
$total = 0;
foreach ($sales as $index => $sale) {
    fputcsv($output, [
        $index + 1,
        $sale['product_name'],
        $sale['quantity'],
        number_format($sale['total_price'], 2),
        $sale['sold_at']
    ]);
    $total += $sale['total_price'];
}

// Summary row
fputcsv($output, []);
fputcsv($output, ['Total Sales', '', '', number_format($total, 2)]);
fputcsv($output, ['Total Transactions', '', '', count($sales)]);

fclose($output);
exit;
?>
