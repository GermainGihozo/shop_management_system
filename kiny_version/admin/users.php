<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require_once 'navbar.php';
requireRole('admin'); // or 'branch'

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$users = $conn->query("
    SELECT u.*, b.name AS branch_name 
    FROM users u 
    LEFT JOIN branches b ON u.branch_id = b.id
    ORDER BY u.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="container mt-5">
  <h4 class="mb-4">User Management</h4>
  <a href="add_user.php" class="btn btn-success mb-3">➕ Add User</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Branch</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
      <tr>
        <td><?= htmlspecialchars($user['full_name']) ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td><?= htmlspecialchars($user['branch_name'] ?? '-') ?></td>
        <td><?= ucfirst(htmlspecialchars($user['status'])) ?></td>
        <td>
          <?php if ($user['status'] == 'active'): ?>
            <a href="deactivate_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Deactivate</a>
          <?php else: ?>
            <a href="activate_user.php?id=<?= $user['id'] ?>" class="btn btn-success btn-sm">Activate</a>
          <?php endif; ?>
          <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
