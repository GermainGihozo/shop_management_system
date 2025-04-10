<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';
require '../includes/auth.php';
requireRole('admin');

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, password = ? WHERE id = ?");
            $stmt->execute([$full_name, $hashed, $user_id]);
            $success = "Profile updated with new password.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $stmt->execute([$full_name, $user_id]);
        $success = "Profile updated.";
    }
}

$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>✏️ Edit Profile</h4>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php elseif (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($user['full_name']) ?>">
    </div>
    <div class="mb-3">
      <label>New Password</label>
      <input type="password" name="new_password" class="form-control">
    </div>
    <div class="mb-3">
      <label>Confirm Password</label>
      <input type="password" name="confirm_password" class="form-control">
    </div>
    <button class="btn btn-primary">💾 Save Changes</button>
    <a href="profile.php" class="btn btn-secondary">Back</a>
  </form>
</div>
</body>
</html>
