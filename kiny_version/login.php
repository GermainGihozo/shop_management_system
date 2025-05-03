<!-- login.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop Management Login</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body {
      background: #f0f2f5;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      width: 100%;
      max-width: 400px;
      padding: 30px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h4 class="text-center mb-4">Shop Login</h4>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form action="login_process.php" method="POST">
      <div class="mb-3">
        <label>Izina</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Ijambobanga</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Injira</button>
    </form>
  </div>
</body>
</html>
