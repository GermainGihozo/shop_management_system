<?php
session_start();
require_once 'navbar.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Branch Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h3>Hello <?= htmlspecialchars($_SESSION['name']) ?> 👋</h3>
    <p>This is your branch dashboard.</p>
    <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
  </div>
</body>
</html>
