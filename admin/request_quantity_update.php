<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

$product_id = $_POST['product_id'];
$requested_quantity = $_POST['requested_quantity'];
$admin_id = $_SESSION['user_id'];

// Get branch_id from product
$stmt = $conn->prepare("SELECT branch_id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$branch_id = $stmt->fetchColumn();

if ($branch_id) {
  $insert = $conn->prepare("INSERT INTO quantity_requests (product_id, branch_id, requested_quantity, requested_by) VALUES (?, ?, ?, ?)");
  $insert->execute([$product_id, $branch_id, $requested_quantity, $admin_id]);
}

header("Location: manage_products.php?msg=Request+sent");
exit;
?>
