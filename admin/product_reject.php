<?php
require '../includes/db.php';
session_start();

if ($_SESSION['role'] !== 'admin') exit;

$id = $_POST['id'];
$reason = trim($_POST['reason']);

$stmt = $conn->prepare("
    UPDATE products 
    SET status = 'rejected', rejection_reason = ?
    WHERE id = ?
");
$stmt->execute([$reason, $id]);

header("Location: products_pending.php?msg=Product rejected");
