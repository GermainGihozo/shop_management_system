<?php
session_start();
require 'includes/db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    if(password_verify($password, $user['password'])){
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['branch_id'] = $user['branch_id'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: branch/dashboard.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "❌ Invalid password.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "❌ Invalid username or password.";
    header("Location: login.php");
    exit;
}
