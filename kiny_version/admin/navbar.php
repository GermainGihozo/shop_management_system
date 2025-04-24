<?php
// session_start(); // Uncomment this if it's not already called earlier
$username = $_SESSION['username'] ?? 'User';
$role = ucfirst($_SESSION['role'] ?? 'Role');
// Adjust the path and extension if needed
$profile_img = '../includes/images/IMG_20230603_085143_694
.jpg'; 
?>

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
        <li class="nav-item"><a class="nav-link" href="users.php">👥 Users</a></li>
        <li class="nav-item"><a class="nav-link" href="sales_report.php">📋 Sales Report</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_products.php">📝 Manage Products</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">🛍 All Products</a></li>
      </ul>

      <!-- Profile Dropdown -->
      <div class="dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" role="button" id="dropdownProfile" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= $profile_img ?>" alt="Profile Picture" class="rounded-circle me-2" style="width: 40px; height: 40px;">
          <span><?= htmlspecialchars($username) ?></span>
          
        </a>
        <!-- <span class="text-success">.Active</span> -->
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
