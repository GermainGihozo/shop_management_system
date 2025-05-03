<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('branch');

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Fetch the current password hash from DB
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $error = "❌ Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "❌ New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $error = "❌ New password must be at least 6 characters.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed, $user_id]);

        $_SESSION['success'] = "✅ Password changed successfully.";
        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Change Password</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
  <h4>🔐 Change Password</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label>Current Password</label>
      <div class="input-group">
        <input type="password" name="current_password" class="form-control" id="currentPassword" required>
        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('currentPassword')">👁️</button>
      </div>
    </div>

    <div class="mb-3">
      <label>New Password</label>
      <div class="input-group">
        <input type="password" name="new_password" class="form-control" id="newPassword" oninput="checkStrength()" required>
        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('newPassword')">👁️</button>
      </div>
      <small id="strengthText" class="text-muted"></small>
      <div class="progress mt-1">
        <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
      </div>
    </div>

    <div class="mb-3">
      <label>Confirm New Password</label>
      <div class="input-group">
        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirmPassword')">👁️</button>
      </div>
    </div>

    <button class="btn btn-primary">Change Password</button>
    <a href="profile.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(id) {
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}

function checkStrength() {
  const password = document.getElementById('newPassword').value;
  const strengthBar = document.getElementById('strengthBar');
  const strengthText = document.getElementById('strengthText');
  let strength = 0;

  if (password.length >= 6) strength += 1;
  if (/[A-Z]/.test(password)) strength += 1;
  if (/[a-z]/.test(password)) strength += 1;
  if (/\d/.test(password)) strength += 1;
  if (/[@$!%*?&#]/.test(password)) strength += 1;

  let strengthColor, strengthLabel;
  switch (strength) {
    case 0:
    case 1:
      strengthColor = "bg-danger";
      strengthLabel = "Very Weak";
      break;
    case 2:
      strengthColor = "bg-warning";
      strengthLabel = "Weak";
      break;
    case 3:
      strengthColor = "bg-info";
      strengthLabel = "Moderate";
      break;
    case 4:
      strengthColor = "bg-primary";
      strengthLabel = "Strong";
      break;
    case 5:
      strengthColor = "bg-success";
      strengthLabel = "Very Strong";
      break;
  }

  strengthBar.style.width = (strength * 20) + "%";
  strengthBar.className = "progress-bar " + strengthColor;
  strengthText.textContent = strengthLabel;
}
</script>
<?php
include'../includes/footer.php';
?>
</body>
</html>
