<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
require 'navbar.php';
requireRole('admin');

// Fetch all branches for the dropdown
$branches = $conn->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Products</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="web icon" type="jpg" href="includes/images/logo.jpg">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    @media (max-width: 768px) {
      .table th, .table td {
        font-size: 14px;
      }
      .form-label {
        margin-bottom: 0.2rem;
      }
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <h4 class="mb-4">🛍️ Product Management</h4>

  <!-- Branch Filter and Live Search -->
  <form class="row g-3 align-items-end mb-4">
    <div class="col-md-4 col-sm-6">
      <label for="branch" class="form-label">Branch:</label>
      <select id="branch" class="form-select">
        <option value="">All Branches</option>
        <?php foreach ($branches as $branch): ?>
          <option value="<?= $branch['id'] ?>"><?= htmlspecialchars($branch['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 col-sm-6">
      <label for="search" class="form-label">Search:</label>
      <input type="text" id="search" class="form-control" placeholder="Search product name...">
    </div>
  </form>

  <!-- Table -->
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Price (RWF)</th>
          <th>Quantity</th>
          <th>Branch</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="product-table-body">
        <!-- AJAX will load rows here -->
      </tbody>
    </table>
  </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
  const branchSelect = document.getElementById('branch');
  const searchInput = document.getElementById('search');
  const tableBody = document.getElementById('product-table-body');

  function loadProducts() {
    const branch = branchSelect.value;
    const search = searchInput.value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `search_products.php?branch=${branch}&search=${encodeURIComponent(search)}`, true);
    xhr.onload = function () {
      if (this.status === 200) {
        tableBody.innerHTML = this.responseText;
      }
    };
    xhr.send();
  }

  // Load initially and on change
  loadProducts();
  branchSelect.addEventListener('change', loadProducts);
  searchInput.addEventListener('keyup', loadProducts);
</script>
<?php
include'../includes/footer.php';
?>
</body>
</html>
