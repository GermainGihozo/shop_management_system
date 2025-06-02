<?php 
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('branch');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM product_update_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request || $request['status'] !== 'pending') {
        die("Invalid or already processed request.");
    }

    if ($action === 'approve') {
        // Update the product quantity
        $stmt = $conn->prepare("UPDATE products SET quantity + ? WHERE id = ?");
        $stmt->execute([$request['new_quantity'], $request['product_id']]);
    }

    // Update request status
    $stmt = $conn->prepare("UPDATE product_update_requests 
                            SET status = ?, reviewed_at = NOW(), reviewed_by_branch_user_id = ?
                            WHERE id = ?");
    $stmt->execute([$action, $user_id, $request_id]);

    header("Location: view_quantity_requests.php?msg=Request " . ucfirst($action) . "d");
    exit;
}
?>