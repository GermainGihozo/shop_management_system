<?php
$current = basename($_SERVER['PHP_SELF']);
function active($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?>

<style>
  @media (max-width: 576px) {
    .navbar-nav .nav-link {
      font-size: 1.1rem; /* larger text on small screens */
      padding-top: 0.75rem;
      padding-bottom: 0.75rem;
    }
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">Branch Panel</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#branchNavbar" aria-controls="branchNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="branchNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-end text-lg-start">
        <li class="nav-item">
          <a class="nav-link <?= active('view_stock.php') ?>" href="view_stock.php">Stock</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active('record_sale.php') ?>" href="record_sale.php">Gurisha</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active('sales_report.php') ?>" href="sales_report.php">Raporo y'ibyacurujwe</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active('profile.php') ?>" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger <?= active('../logout.php') ?>" href="../logout.php">Sohoka</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
