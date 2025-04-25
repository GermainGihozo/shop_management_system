<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $admin_id = $_SESSION['user_id'];

    if ($admin_id == $id) {
        $_SESSION['error'] = "You can't delete your own account.";
        header("Location: users.php");
        exit;
    }

    try {
        // Start transaction
        $conn->beginTransaction();

        // Fetch username before deletion for logging
        $stmtUser = $conn->prepare("SELECT username FROM users WHERE id = :id");
        $stmtUser->execute([':id' => $id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            header("Location: users.php");
            exit;
        }

        $username = $user['username'];

        // Delete logs related to the user
        $deleteLogs = $conn->prepare("DELETE FROM user_logs WHERE user_id = :id OR target_user_id = :id");
        $deleteLogs->execute([':id' => $id]);

        // Delete the user
        $deleteUser = $conn->prepare("DELETE FROM users WHERE id = :id");
        $deleteUser->execute([':id' => $id]);

        // Log this action
        $log = $conn->prepare("INSERT INTO user_logs (user_id, action, target_user_id) VALUES (:admin_id, :action, :target_id)");
        $action = "Deleted user '$username'";
        $log->execute([
            ':admin_id' => $admin_id,
            ':action' => $action,
            ':target_id' => $id
        ]);

        $conn->commit();
        $_SESSION['success'] = "User '$username' and related logs deleted successfully.";
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
}

header("Location: users.php");
exit;
