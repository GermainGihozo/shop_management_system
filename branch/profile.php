<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('branch');

$user_id = $_SESSION['user_id'];

// Fetch branch user info
$stmt = $conn->prepare("SELECT full_name, username, role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle success message from password change
$success = $_SESSION['success'] ?? "";
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Profile</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">
  <h4>👤 My Profile</h4>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <table class="table table-bordered mt-4">
    <tr>
      <th>Name</th>
      <td><?= htmlspecialchars($user['full_name']) ?></td>
    </tr>
    <tr>
      <th>Username</th>
      <td><?= htmlspecialchars($user['username']) ?></td>
    </tr>
    <!--  -->
    <tr>
      <th>Role</th>
      <td><?= htmlspecialchars($user['role']) ?></td>
    </tr>
  </table>

  <a href="change_password.php" class="btn btn-warning mt-2">🔐 Change Password</a>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
