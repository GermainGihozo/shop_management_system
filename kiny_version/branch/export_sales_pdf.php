<?php
session_start();
require '../includes/db.php';
require('../libs/tfpdf/tfpdf.php');

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

// Initialize PDF
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->SetFont('DejaVu','',12);

$pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('DejaVu','',10);
$pdf->SetFillColor(220,220,220);
$pdf->Cell(10, 10, '#', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Product', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Total (RWF)', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Sale Date', 1, 1, 'C', true);

$total = 0;
foreach ($sales as $index => $sale) {
    $pdf->Cell(10, 10, $index + 1, 1);
    $pdf->Cell(60, 10, $sale['product_name'], 1);
    $pdf->Cell(30, 10, $sale['quantity'], 1);
    $pdf->Cell(40, 10, number_format($sale['total_price'], 2), 1);
    $pdf->Cell(45, 10, $sale['sold_at'], 1, 1);
    $total += $sale['total_price'];
}

// Summary
$pdf->Ln(5);
$pdf->SetFont('DejaVu','',11);
$pdf->Cell(0, 10, 'Total Sales: RWF ' . number_format($total, 2), 0, 1);
$pdf->Cell(0, 10, 'Total Transactions: ' . count($sales), 0, 1);

$pdf->Output('D', 'sales_report.pdf');
exit;
?>
