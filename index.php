<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <title>Shop Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="admin/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: url('admin/images/bg-pattern.png') repeat;
      background-size: 300px;
      transition: background-color 0.3s ease;
    }

    main {
      flex: 1;
    }

    .hero {
      background: linear-gradient(to right, #0d6efd, #6610f2);
      color: white;
      padding: 60px 20px;
      text-align: center;
      border-radius: 10px;
      animation: slideInDown 1s ease;
      position: relative;
    }

    @keyframes slideInDown {
      from { opacity: 0; transform: translateY(-40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card {
      transition: transform 0.3s, box-shadow 0.3s;
      border-radius: 16px;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-animated {
      transition: all 0.3s ease;
    }

    .btn-animated:hover {
      transform: scale(1.05);
    }

    .fade-in {
      animation: fadeIn 1.5s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .footer {
      text-align: center;
      padding: 15px;
      background: #f8f9fa;
    }

    .dark-mode .footer {
      background: #212529;
      color: #ddd;
    }

    .theme-toggle {
      position: absolute;
      top: 15px;
      right: 15px;
      background: white;
      border: none;
      border-radius: 50%;
      width: 38px;
      height: 38px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
      z-index: 10;
    }
  </style>
</head>
<body>

<main class="container my-5 fade-in">
  <div class="hero mb-5 position-relative">
    <h1 class="display-5">Welcome to the Shop Management System</h1>
    <p class="lead">Manage branches, products, and sales effortlessly.</p>

    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light mode">
      🌓
    </button>
  </div>

  <div class="row justify-content-center text-center">
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Admin Login</h5>
          <p class="card-text">Manage all branches, users, and inventory.</p>
          <a href="login.php" class="btn btn-primary btn-animated w-100">Admin Panel</a>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Branch Login</h5>
          <p class="card-text">Access your branch dashboard and manage sales.</p>
          <a href="login.php" class="btn btn-success btn-animated w-100">Branch Panel</a>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="footer">
  <p class="text-muted mb-0">© <?= date('Y') ?> Shop Management System. All rights reserved.</p>
</footer>

<script src="admin/js/bootstrap.bundle.min.js"></script>
<script>
  // Theme toggle
  function toggleTheme() {
    const html = document.documentElement;
    const newTheme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    document.body.classList.toggle('dark-mode', newTheme === 'dark');
  }
</script>
</body>
</html>
