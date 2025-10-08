<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['branch_id'])) {
    echo json_encode([]);
    exit;
}

$branch_id = $_SESSION['branch_id'];
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query !== '') {
    $stmt = $conn->prepare("SELECT id, name, quantity, price FROM products WHERE branch_id = ? AND name LIKE ?");
    $stmt->execute([$branch_id, "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>
