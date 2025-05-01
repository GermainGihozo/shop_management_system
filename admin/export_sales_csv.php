<?php
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$branch_id = $_GET['branch_id'] ?? 'all';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Branch', 'Sale Date', 'Total Quantity Sold', 'Total Sales (RWF)']);

$sql = "SELECT b.name AS branch_name, DATE(s.sold_at) AS sale_day,
               SUM(s.total_price) AS total_sales, SUM(s.quantity) AS total_quantity
        FROM sales s
        JOIN branches b ON s.branch_id = b.id
        WHERE DATE(s.sold_at) BETWEEN :start_date AND :end_date";

if ($branch_id !== 'all') {
    $sql .= " AND s.branch_id = :branch_id";
}

$sql .= " GROUP BY s.branch_id, DATE(s.sold_at)
          ORDER BY sale_day DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
if ($branch_id !== 'all') {
    $stmt->bindParam(':branch_id', $branch_id);
}
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['branch_name'],
        $row['sale_day'],
        $row['total_quantity'],
        number_format($row['total_sales'], 2)
    ]);
}

fclose($output);
exit;
