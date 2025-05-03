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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $branch_name = trim($_POST['branch_name']);
    $branch_id = null;

    if ($role === 'branch' && !empty($branch_name)) {
        // Check if branch exists
        $stmt = $conn->prepare("SELECT id FROM branches WHERE name = ?");
        $stmt->execute([$branch_name]);
        $branch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($branch) {
            $branch_id = $branch['id'];
        } else {
            // Insert new branch
            $insert = $conn->prepare("INSERT INTO branches (name) VALUES (?)");
            $insert->execute([$branch_name]);
            $branch_id = $conn->lastInsertId();
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, branch_id, full_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $password, $role, $branch_id, $full_name]);

    header("Location: users.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    footer {
        margin-top: auto;
    }
</style>

</head>
<body>
<div class="container mt-5">
  <h4 class="mb-4">➕ Add New User</h4>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="full_name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" class="form-select" required onchange="toggleBranch(this.value)">
        <option value="admin">Admin</option>
        <option value="branch">Branch User</option>
      </select>
    </div>

    <div class="mb-3" id="branchInput" style="display: none;">
      <label class="form-label">Branch Name</label>
      <input type="text" name="branch_name" class="form-control" placeholder="e.g. Downtown">
    </div>

    <button type="submit" class="btn btn-primary">✅ Create User</button>
  </form>
</div>

<script>
function toggleBranch(role) {
  const branchDiv = document.getElementById('branchInput');
  branchDiv.style.display = (role === 'branch') ? 'block' : 'none';
}
</script>
<script src="js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
