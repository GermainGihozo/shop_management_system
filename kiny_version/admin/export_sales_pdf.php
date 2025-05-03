<?php
require '../includes/db.php';
require '../libs/tfpdf/tfpdf.php'; // Make sure the path is correct

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$selected_branch = $_GET['branch_id'] ?? 'all';

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
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create PDF
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->SetFont('DejaVu','',12);

// Header
$pdf->Cell(0,10,"Sales Report (From $start_date to $end_date)",0,1,'C');
$pdf->Ln(5);

// Table Header
$pdf->SetFillColor(220,220,220);
$pdf->Cell(10,10,'#',1,0,'C',true);
$pdf->Cell(50,10,'Branch',1,0,'C',true);
$pdf->Cell(40,10,'Sale Date',1,0,'C',true);
$pdf->Cell(40,10,'Total Qty',1,0,'C',true);
$pdf->Cell(50,10,'Total Sales (RWF)',1,1,'C',true);

$count = 1;
$grand_total = 0;

foreach ($sales as $row) {
    $pdf->Cell(10,10,$count++,1);
    $pdf->Cell(50,10,$row['branch_name'],1);
    $pdf->Cell(40,10,$row['sale_day'],1);
    $pdf->Cell(40,10,$row['total_quantity'],1);
    $pdf->Cell(50,10,number_format($row['total_sales'],2),1,1);
    $grand_total += $row['total_sales'];
}

// Summary
$pdf->Ln(5);
$pdf->SetFont('DejaVu','',12);
$pdf->Cell(0,10,"Total Revenue: RWF " . number_format($grand_total, 2),0,1);

$pdf->Output('D', 'sales_report.pdf');
exit;
?>
