<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';

requireRole('admin');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$userId = $_GET['id'];

if ($userId == $_SESSION['user_id']) {
    header("Location: users.php?msg=You can't activate your own account.");
    // die("You can't activate your own account.");
}

// Activate user
$stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
if ($stmt->execute([$userId])) {
    // Log action
    $log = $conn->prepare("INSERT INTO user_logs (admin_id, target_user_id, action) VALUES (?, ?, 'activated')");
    $log->execute([$_SESSION['user_id'], $userId]);

    header("Location: users.php?msg=User activated successfully");
    exit;
} else {
    echo "Failed to activate user.";
}
