<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';

requireRole('admin');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$userId = $_GET['id'];

// Optional: Prevent self-deactivation
if ($userId == $_SESSION['user_id']) {
    die("You can't deactivate your own account.");
    
}

// Deactivate user
$stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
if ($stmt->execute([$userId])) {
    // Log action
    $log = $conn->prepare("INSERT INTO user_logs (admin_id, target_user_id, action) VALUES (?, ?, 'deactivated')");
    $log->execute([$_SESSION['user_id'], $userId]);

    header("Location: users.php?msg=User deactivated successfully");
    exit;
} else {
    echo "Failed to deactivate user.";
}
