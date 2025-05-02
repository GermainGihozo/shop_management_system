<?php
// session_start(); // Uncomment if session not started elsewhere
$username = $_SESSION['username'] ?? 'User';
$role = ucfirst($_SESSION['role'] ?? 'Role');
$profile_img = '../includes/images/IMG_20230603_085143_694.jpg'; 

$current_page = basename($_SERVER['PHP_SELF']); // Get current filename
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> -->
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">🏠 Admin Panel</a>

    <!-- Toggler Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'users.php' ? 'active' : '' ?>" href="users.php">👥 Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'sales_report.php' ? 'active' : '' ?>" href="sales_report.php">📋 Sales Report</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'manage_products.php' ? 'active' : '' ?>" href="manage_products.php">📝 Manage Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="products.php">🛍 All Products</a>
        </li>
      </ul>

      <!-- Profile Dropdown -->
      <div class="dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" role="button" id="dropdownProfile" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= $profile_img ?>" alt="Profile Picture" class="rounded-circle me-2" style="width: 40px; height: 40px;">
          <span><?= htmlspecialchars($username) ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownProfile">
          <li><h6 class="dropdown-header"><?= htmlspecialchars($role) ?></h6></li>
          <li><a class="dropdown-item" href="profile.php">👤 Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="../logout.php">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Add Bootstrap Bundle JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script> -->

</body>
</html>
