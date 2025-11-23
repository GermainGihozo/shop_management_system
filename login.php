<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HimShop Login</title>

  <link rel="stylesheet" href="css/bootstrap.min.css">

  <style>
    body {
      background: #f0f2f5;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      font-family: "Segoe UI", sans-serif;
    }

    .login-box {
      width: 100%;
      max-width: 400px;
      background: white;
      padding: 35px 30px;
      border-radius: 15px;
      box-shadow: 0px 10px 25px rgba(0,0,0,0.08);
      animation: fadeIn .7s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .logo {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
      border: 3px solid #ffc107;
    }

    .title {
      font-weight: 700;
      color: #333;
    }

    .subtitle {
      color: #555;
      font-size: 14px;
      margin-bottom: 20px;
    }
  </style>

</head>
<body>

  <div class="login-box text-center">

    <!-- LOGO -->
    <img src="includes/images/logo.jpg" alt="Logo" class="logo">

    <h4 class="title">Welcome Back 👋</h4>
    <p class="subtitle">Login to access your shop dashboard</p>

    <!-- Error message -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger py-2">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST" class="text-start mt-3">

      <div class="mb-3">
        <label class="fw-semibold">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="fw-semibold">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button class="btn btn-warning w-100 fw-bold py-2">Login</button>

    </form>

    <p class="mt-3 small text-muted">
      Powered by <span class="fw-bold text-warning">HimShop</span>
    </p>

  </div>

</body>
</html>
