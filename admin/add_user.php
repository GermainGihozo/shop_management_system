<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('admin');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$branches = $conn->query("SELECT * FROM branches");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $branch_id = ($role === 'branch') ? $_POST['branch_id'] : null;

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, branch_id, full_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $username, $password, $role, $branch_id, $full_name);
    $stmt->execute();

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h4>Add New User</h4>
  <form method="POST">
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="full_name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-select" required>
        <option value="admin">Admin</option>
        <option value="branch">Branch User</option>
      </select>
    </div>

    <div class="mb-3">
      <label>Branch (only if Branch User)</label>
      <select name="branch_id" class="form-select">
        <option value="">-- Select Branch --</option>
        <?php while ($b = $branches->fetch_assoc()): ?>
          <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Create User</button>
  </form>
</div>
</body>
</html>
