<?php
$current = basename($_SERVER['PHP_SELF']);
function active($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">Branch Panel</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#branchNavbar" aria-controls="branchNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="branchNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= active('view_stock.php') ?>" href="view_stock.php">Stock</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active('record_sale.php') ?>" href="record_sale.php">Sell</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active('sales_report.php') ?>" href="sales_report.php">Sales Report</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active('profile.php') ?>" href="profile.php">Profile</a>
        </li>
        <li><a class="nav-item" href="branch_profile.php">👤 My Profile</a></li>

        <li class="nav-item">
          <a class="nav-link text-danger <?= active('../logout.php') ?>" href="../logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
