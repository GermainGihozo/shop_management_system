<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];

// Fetch all products for this branch
$stmt = $conn->prepare("SELECT name, quantity, price FROM products WHERE branch_id = ? ORDER BY name ASC");
$stmt->execute([$branch_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    .pagination { justify-content: center; }
    .search-box { max-width: 400px; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <h4>📦 All Products</h4>

  <div class="d-flex justify-content-between align-items-center mt-3">
    <input type="text" id="searchInput" class="form-control search-box" placeholder="🔍 Search products...">
    <!-- <a href="dashboard.php" class="btn btn-secondary ms-3">← Dashboard</a> -->
  </div>

  <?php if (count($products) === 0): ?>
    <div class="alert alert-warning mt-4">⚠️ No products found for this branch.</div>
  <?php else: ?>
    <div class="table-responsive mt-3">
      <table class="table table-bordered table-hover" id="productTable">
        <thead class="table-dark">
          <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (RWF)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): ?>
            <tr>
              <td><?= htmlspecialchars($product['name']) ?></td>
              <td><?= $product['quantity'] ?></td>
              <td><?= number_format($product['price'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <a href="dashboard.php" class="btn btn-secondary ms-3">← Dashboard</a>
    </div>

    <!-- Pagination -->
    <nav>
      <ul class="pagination" id="pagination"></ul>
    </nav>
  <?php endif; ?>
</div>

<script>
// Pagination & Search (Vanilla JS)
const rowsPerPage = 10;
const table = document.getElementById('productTable').getElementsByTagName('tbody')[0];
const rows = Array.from(table.getElementsByTagName('tr'));
const pagination = document.getElementById('pagination');

function displayRows(page) {
  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  rows.forEach((row, index) => {
    row.style.display = index >= start && index < end ? '' : 'none';
  });
}

function setupPagination() {
  pagination.innerHTML = '';
  const pageCount = Math.ceil(rows.length / rowsPerPage);
  for (let i = 1; i <= pageCount; i++) {
    const li = document.createElement('li');
    li.className = 'page-item';
    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    li.addEventListener('click', (e) => {
      e.preventDefault();
      displayRows(i);
      document.querySelectorAll('.page-item').forEach(item => item.classList.remove('active'));
      li.classList.add('active');
    });
    pagination.appendChild(li);
  }
  if (pagination.firstChild) pagination.firstChild.classList.add('active');
  displayRows(1);
}

function filterTable() {
  const query = document.getElementById('searchInput').value.toLowerCase();
  let visibleRows = 0;
  rows.forEach(row => {
    const match = Array.from(row.cells).some(td =>
      td.textContent.toLowerCase().includes(query)
    );
    row.style.display = match ? '' : 'none';
    if (match) visibleRows++;
  });
  // Hide pagination if filtering
  pagination.style.display = query ? 'none' : 'flex';
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);
setupPagination();
</script>

</body>
</html>
