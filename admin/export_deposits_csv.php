<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

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

// Output CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="deposits.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Branch', 'Amount (RWF)']);

foreach ($deposits as $row) {
  fputcsv($output, [$row['deposit_date'], $row['branch_name'], $row['amount']]);
}
fclose($output);
exit;
