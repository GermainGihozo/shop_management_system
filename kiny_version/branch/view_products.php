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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    .pagination { justify-content: center; }
    .search-box { max-width: 400px; }
    @media (max-width: 576px) {
      .search-box {
        width: 100%;
      }
      .table {
        font-size: 0.9rem;
      }
      .btn {
        width: 100%;
        margin-top: 10px;
      }
    }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <h4 class="mb-3">📦 Ibicuruzwa byose</h4>

  <div class="row mb-3">
    <div class="col-12 col-md-6">
      <input type="text" id="searchInput" class="form-control search-box" placeholder="🔍 Shaka Ibicuruzwa...">
    </div>
  </div>

  <?php if (count($products) === 0): ?>
    <div class="alert alert-warning mt-4">⚠️ Nta gicuruzwa kirimo.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="productTable">
        <thead class="table-dark">
          <tr>
            <th>Izina ry'igicuruzwa</th>
            <th>Ingano</th>
            <th>Igiciro (RWF)</th>
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
    </div>

    <nav>
      <ul class="pagination mt-3" id="pagination"></ul>
    </nav>

    <a href="dashboard.php" class="btn btn-secondary mt-2">← ahabanza</a>
  <?php endif; ?>
</div>
<?php
include'../includes/footer.php';
?>
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
  pagination.style.display = query ? 'none' : 'flex';
}

document.getElementById('searchInput').addEventListener('keyup', filterTable);
setupPagination();
</script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
