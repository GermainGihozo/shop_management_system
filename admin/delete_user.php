<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin'); // or 'branch'


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($_SESSION['user_id'] == $id) {
        $_SESSION['error'] = "You can't delete your own account.";
        header("Location: users.php");
        exit;
    }

    $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "User not found.";
    }
}

header("Location: users.php");
