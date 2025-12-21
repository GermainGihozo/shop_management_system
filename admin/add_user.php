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

    $full_name   = trim($_POST['full_name']);
    $username    = trim($_POST['username']);
    $passwordRaw = $_POST['password'];
    $role        = $_POST['role'];
    $branch_name = trim($_POST['branch_name']);
    $branch_id   = null;

    // ---------------- VALIDATION ----------------
    if (empty($full_name) || empty($username) || empty($passwordRaw)) {
        $_SESSION['error'] = "❌ All required fields must be filled.";
        header("Location: add_user.php");
        exit;
    }

    if (strlen($passwordRaw) < 6) {
        $_SESSION['error'] = "❌ Password must be at least 6 characters.";
        header("Location: add_user.php");
        exit;
    }

    // -------- CHECK DUPLICATE USERNAME ----------
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$username]);

    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "❌ Username already exists. Choose another one.";
        header("Location: add_user.php");
        exit;
    }

    // ---------------- BRANCH LOGIC ----------------
    if ($role === 'branch' && !empty($branch_name)) {

        $stmt = $conn->prepare("SELECT id FROM branches WHERE name = ?");
        $stmt->execute([$branch_name]);
        $branch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($branch) {
            $branch_id = $branch['id'];
        } else {
            $insert = $conn->prepare("INSERT INTO branches (name) VALUES (?)");
            $insert->execute([$branch_name]);
            $branch_id = $conn->lastInsertId();
        }
    }

    // ---------------- INSERT USER ----------------
    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (username, password, role, branch_id, full_name)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $username,
        $password,
        $role,
        $branch_id,
        $full_name
    ]);

    $_SESSION['success'] = "✅ User created successfully!";
    header("Location: add_user.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background: #f8f9fa;
    }
    footer {
        margin-top: auto;
    }
    .card {
        border-radius: 12px;
    }
  </style>
</head>

<body>

<div class="container mt-5" style="max-width: 600px;">
  <div class="card shadow-sm">
    <div class="card-body">

      <h4 class="mb-4 text-center">➕ Add New User</h4>

      <!-- Alerts -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">

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
          <small class="text-muted">Minimum 6 characters</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required onchange="toggleBranch(this.value)">
            <option value="admin">Admin</option>
            <option value="branch">Branch User</option>
          </select>
        </div>

        <div class="mb-3" id="branchInput" style="display:none;">
          <label class="form-label">Branch Name</label>
          <input type="text" name="branch_name" class="form-control" placeholder="e.g. Downtown">
        </div>

        <button type="submit" class="btn btn-primary w-100">
          ✅ Create User
        </button>

      </form>

    </div>
  </div>
</div>

<script>
function toggleBranch(role) {
    const branchDiv = document.getElementById('branchInput');
    branchDiv.style.display = role === 'branch' ? 'block' : 'none';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
