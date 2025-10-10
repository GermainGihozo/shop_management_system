<?php
require '../includes/db.php';

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$selected_branch = $_GET['branch_id'] ?? 'all';

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=sales_report.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Branch', 'Sale Date', 'Total Quantity', 'Total Sales']);

$sql = "SELECT b.name AS branch_name, DATE(s.sold_at) AS sale_day,
               SUM(s.quantity * p.price) AS total_sales,
               SUM(s.quantity) AS total_quantity
        FROM sales s
        JOIN branches b ON s.branch_id = b.id
        JOIN products p ON s.product_id = p.id
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
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $row) {
    fputcsv($output, [
        $row['branch_name'],
        $row['sale_day'],
        $row['total_quantity'],
        number_format($row['total_sales'], 2)
    ]);
}
fclose($output);
exit;
?>
