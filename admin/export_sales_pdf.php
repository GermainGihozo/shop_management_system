<?php
session_start();
require '../includes/db.php';
require '../libs/tfpdf/tfpdf.php'; // ✅ Path to tfpdf.php
require '../includes/auth.php';
requireRole('admin'); // or 'branch'


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$filter = $_GET['filter'] ?? 'daily';
$branch_filter = $_GET['branch'] ?? 'all';

// 🔍 Time filter
$date_filter = "";
switch ($filter) {
    case 'weekly':
        $date_filter = "AND YEARWEEK(sold_at) = YEARWEEK(CURDATE())";
        break;
    case 'monthly':
        $date_filter = "AND MONTH(sold_at) = MONTH(CURDATE()) AND YEAR(sold_at) = YEAR(CURDATE())";
        break;
    default:
        $date_filter = "AND DATE(sold_at) = CURDATE()";
        break;
}

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

$params = [];
$types = "";

if ($branch_filter !== 'all') {
    $query .= " AND sales.branch_id = ? ";
    $params[] = $branch_filter;
    $types .= "i";
}

$query .= " $date_filter ORDER BY sales.sold_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();


// ✅ Initialize TFPDF
$pdf = new tFPDF('L', 'mm', 'A4');
$pdf->AddPage();

// 🔤 Load a Unicode font (Roboto or FreeSerif)
$pdf->AddFont('FreeSerif', '', 'FreeSerif.php');
$pdf->SetFont('FreeSerif', '', 12);

// 📄 Title
$pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
$pdf->Ln(5);

// 🧾 Table headers
$pdf->SetFont('FreeSerif', 'B', 11);
$pdf->Cell(40, 10, 'Branch', 1);
$pdf->Cell(50, 10, 'Product', 1);
$pdf->Cell(20, 10, 'Qty', 1);
$pdf->Cell(30, 10, 'Price (RWF)', 1);
$pdf->Cell(35, 10, 'Total (RWF)', 1);
$pdf->Cell(45, 10, 'Date', 1);
$pdf->Ln();

// 🔁 Loop through results
$pdf->SetFont('FreeSerif', '', 11);
$total_sales = 0;

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['branch'], 1);
    $pdf->Cell(50, 10, $row['product'], 1);
    $pdf->Cell(20, 10, $row['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['price_each'], 2), 1, 0, 'R');
    $pdf->Cell(35, 10, number_format($row['total_price'], 2), 1, 0, 'R');
    $pdf->Cell(45, 10, $row['sold_at'], 1);
    $pdf->Ln();

    $total_sales += $row['total_price'];
}

// ➕ Total
$pdf->SetFont('FreeSerif', 'B', 12);
$pdf->Cell(140, 10, 'Total Sales', 1);
$pdf->Cell(35, 10, number_format($total_sales, 2), 1, 0, 'R');
$pdf->Cell(45, 10, 'RWF', 1);
$pdf->Ln();

$pdf->Output('D', 'sales_report.pdf');
exit;
