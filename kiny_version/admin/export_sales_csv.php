<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin'); // or 'branch'


// ✅ Only allow admins to export sales data
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

// 🔍 Get filters from query parameters
$filter = $_GET['filter'] ?? 'daily';
$branch_filter = $_GET['branch'] ?? 'all';

// 🕒 Set date condition based on filter
$date_filter = "";
switch ($filter) {
    case 'weekly':
        $date_filter = "AND YEARWEEK(sold_at) = YEARWEEK(CURDATE())";
        break;
    case 'monthly':
        $date_filter = "AND MONTH(sold_at) = MONTH(CURDATE()) AND YEAR(sold_at) = YEAR(CURDATE())";
        break;
    default: // daily
        $date_filter = "AND DATE(sold_at) = CURDATE()";
        break;
}

// 📦 Build main query to get sales + related info
$query = "SELECT 
            branches.name AS branch, 
            products.name AS product, 
            sales.quantity, 
            sales.price_each, 
            sales.total_price, 
            sales.sold_at 
          FROM sales 
          JOIN products ON sales.product_id = products.id 
          JOIN branches ON sales.branch_id = branches.id 
          WHERE 1=1 ";

// 👀 Prepare optional filters
$params = [];
$types = "";

if ($branch_filter !== 'all') {
    $query .= " AND sales.branch_id = ? ";
    $params[] = $branch_filter;
    $types .= "i";
}

$query .= " $date_filter ORDER BY sales.sold_at DESC";

// 🔐 Prepare and run SQL statement
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// 📤 Tell browser to download as CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="sales_report.csv"');

// 📝 Open output stream and write CSV headers
$output = fopen('php://output', 'w');
fputcsv($output, ['Branch', 'Product', 'Quantity', 'Price Each (RWF)', 'Total (RWF)', 'Date']);

// 📊 Write each row to the CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['branch'],
        $row['product'],
        $row['quantity'],
        number_format($row['price_each'], 2),
        number_format($row['total_price'], 2),
        $row['sold_at']
    ]);
}

// ✅ Done – close output
fclose($output);
exit;
