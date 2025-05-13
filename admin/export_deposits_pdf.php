<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');
require('../libs/tfpdf/tfpdf.php');

$branch_id = $_GET['branch_id'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$query = "SELECT d.*, b.name AS branch_name FROM deposits d JOIN branches b ON d.branch_id = b.id WHERE 1=1";
$params = [];

if ($branch_id) {
  $query .= " AND d.branch_id = ?";
  $params[] = $branch_id;
}
if ($from_date) {
  $query .= " AND d.deposit_date >= ?";
  $params[] = $from_date;
}
if ($to_date) {
  $query .= " AND d.deposit_date <= ?";
  $params[] = $to_date;
}

$query .= " ORDER BY d.deposit_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu','','DejaVuSans.ttf',true);
$pdf->SetFont('DejaVu','',12);

$pdf->Cell(0, 10, 'Branch Deposits Report', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFillColor(200,200,200);
$pdf->Cell(50, 10, 'Date', 1, 0, 'C', true);
$pdf->Cell(70, 10, 'Branch', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Amount (RWF)', 1, 1, 'C', true);

$total = 0;

foreach ($deposits as $row) {
  $pdf->Cell(50, 10, $row['deposit_date'], 1);
  $pdf->Cell(70, 10, $row['branch_name'], 1);
  $pdf->Cell(50, 10, number_format($row['amount'], 2), 1, 1);
  $total += $row['amount'];
}

$pdf->SetFont('DejaVu','B',12);
$pdf->Cell(120, 10, 'Total', 1, 0, 'R');
$pdf->Cell(50, 10, number_format($total, 2).' RWF', 1, 1, 'R');

$pdf->Output('D', 'deposits.pdf');
exit;
