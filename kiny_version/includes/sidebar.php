<!-- Sidebar -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark flex-column align-items-start vh-100 p-1" style="width: 200px;">
  <a class="navbar-brand mb-1" href="#">
    🛍️ Shop Manager
  </a>
  <?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
  <button class="navbar-toggler mb-4" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse w-100" id="sidebarMenu">
  <ul class="navbar-nav flex-column w-100">
  <li class="nav-item">
    <a class="nav-link text-white <?= ($current_page == 'dashboard.php') ? 'active bg-secondary' : '' ?>" href="dashboard.php">📊 Dashboard</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-white <?= ($current_page == 'sales_report.php') ? 'active bg-secondary' : '' ?>" href="sales_report.php">📈 Sales Report</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-white <?= ($current_page == 'profile.php') ? 'active bg-secondary' : '' ?>" href="profile.php">👤 Profile</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-white" href="../logout.php">🚪 Logout</a>
  </li>
</ul>

  </div>
</nav>
