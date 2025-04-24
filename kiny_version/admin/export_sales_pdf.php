<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

require('../libs/tfpdf/tfpdf.php');

$filter = $_GET['filter'] ?? 'daily';
$branch_filter = $_GET['branch'] ?? 'all';

// Build dynamic query
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

$query = "SELECT sales.*, products.name AS product_name, branches.name AS branch_name 
          FROM sales 
          JOIN products ON sales.product_id = products.id 
          JOIN branches ON sales.branch_id = branches.id 
          WHERE 1=1 ";

$params = [];

// Add branch filter
if ($branch_filter !== 'all') {
    $query .= " AND sales.branch_id = ?";
    $params[] = $branch_filter;
}

$query .= " $date_filter ORDER BY sales.sold_at DESC";
$stmt = $conn->prepare($query);

// Bind values
foreach ($params as $index => $param) {
    $stmt->bindValue($index + 1, $param, PDO::PARAM_INT);
}

$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create PDF
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->SetFont('DejaVu','',12);

// Title
$pdf->Cell(0, 10, '🧾 Sales Report', 0, 1, 'C');
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('DejaVu','',10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(30, 10, 'Branch', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Product', 1, 0, 'C', true);
$pdf->Cell(15, 10, 'Qty', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Price Each', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Total', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Sold At', 1, 1, 'C', true);

$total_sales = 0;

foreach ($sales as $row) {
    $pdf->Cell(30, 10, $row['branch_name'], 1);
    $pdf->Cell(40, 10, $row['product_name'], 1);
    $pdf->Cell(15, 10, $row['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($row['price_each'], 2), 1, 0, 'R');
    $pdf->Cell(30, 10, number_format($row['total_price'], 2), 1, 0, 'R');
    $pdf->Cell(40, 10, $row['sold_at'], 1, 1);
    $total_sales += $row['total_price'];
}

// Total
$pdf->SetFont('DejaVu','',11);
$pdf->Cell(115, 10, 'Total Sales', 1, 0, 'R', true);
$pdf->Cell(30, 10, number_format($total_sales, 2), 1, 0, 'R', true);
$pdf->Cell(40, 10, 'RWF', 1, 1, 'C', true);

$pdf->Output('D', 'sales_report.pdf');
exit;
