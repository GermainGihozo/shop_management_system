<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';
require '../includes/auth.php';
requireRole('admin');

// Get current user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT u.*, b.name AS branch_name FROM users u LEFT JOIN branches b ON u.branch_id = b.id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>👤 My Profile</h4>
  <table class="table table-bordered">
    <tr>
      <th>Full Name</th>
      <td><?= htmlspecialchars($user['full_name']) ?></td>
    </tr>
    <tr>
      <th>Username</th>
      <td><?= htmlspecialchars($user['username']) ?></td>
    </tr>
    <tr>
      <th>Role</th>
      <td><?= htmlspecialchars($user['role']) ?></td>
    </tr>
    <?php if ($user['role'] === 'branch'): ?>
    <tr>
      <th>Branch</th>
      <td><?= htmlspecialchars($user['branch_name']) ?></td>
    </tr>
    <?php endif; ?>
  </table>

  <a href="edit_profile.php" class="btn btn-warning">✏️ Edit Profile</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
