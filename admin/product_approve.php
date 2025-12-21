<?php
require '../includes/db.php';
session_start();

if ($_SESSION['role'] !== 'admin') exit;

$id = $_GET['id'];

$stmt = $conn->prepare("
    UPDATE products 
    SET status = 'approved', approved_at = NOW(), rejection_reason = NULL
    WHERE id = ?
");
$stmt->execute([$id]);

header("Location: products_pending.php?msg=Product approved successfully");
