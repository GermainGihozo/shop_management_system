<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

// Handle optional status filter
$filter = $_GET['filter'] ?? 'all';
$allowed_filters = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter, $allowed_filters)) $filter = 'all';

// Build query
$sql = "
  SELECT r.*, p.name AS product_name, b.name AS branch_name, u.username AS admin_username,
         ru.username AS branch_username
  FROM product_update_requests r
  JOIN products p ON r.product_id = p.id
  JOIN branches b ON r.branch_id = b.id
  JOIN users u ON r.requested_by_admin_id = u.id
  LEFT JOIN users ru ON r.reviewed_by_branch_user_id = ru.id
";

$params = [];
if ($filter !== 'all') {
  $sql .= " WHERE r.status = ?";
  $params[] = $filter;
}

$sql .= " ORDER BY r.requested_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Quantity Update Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media (max-width: 576px) {
      h4 {
        font-size: 1.2rem;
      }
      .btn {
        margin-bottom: 5px;
      }
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <h4 class="mb-3">📊 All Quantity Update Requests</h4>

  <!-- Filter Buttons -->
  <div class="d-flex flex-wrap gap-2 mb-3">
    <a href="?filter=all" class="btn btn-outline-primary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
    <a href="?filter=pending" class="btn btn-outline-warning <?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
    <a href="?filter=approved" class="btn btn-outline-success <?= $filter === 'approved' ? 'active' : '' ?>">Approved</a>
    <a href="?filter=rejected" class="btn btn-outline-danger <?= $filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>Product</th>
          <th>Branch</th>
          <th>Requested By</th>
          <th>Requested At</th>
          <th>Quantity to Add</th>
          <th>Status</th>
          <th>Reviewed By</th>
          <th>Reviewed At</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($requests) === 0): ?>
          <tr><td colspan="8" class="text-center text-muted">No requests found.</td></tr>
        <?php else: ?>
          <?php foreach ($requests as $req): ?>
          <tr class="<?php
            if ($req['status'] === 'approved') echo 'table-success';
            elseif ($req['status'] === 'rejected') echo 'table-danger';
            elseif ($req['status'] === 'pending') echo 'table-warning';
          ?>">
            <td><?= htmlspecialchars($req['product_name']) ?></td>
            <td><?= htmlspecialchars($req['branch_name']) ?></td>
            <td><?= htmlspecialchars($req['admin_username']) ?></td>
            <td><?= date('Y-m-d H:i', strtotime($req['requested_at'])) ?></td>
            <td><?= $req['added_quantity'] ?></td>
            <td>
              <?php
                if ($req['status'] === 'pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                elseif ($req['status'] === 'approved') echo '<span class="badge bg-success">Approved</span>';
                else echo '<span class="badge bg-danger">Rejected</span>';
              ?>
            </td>
            <td><?= $req['branch_username'] ?? '—' ?></td>
            <td><?= $req['reviewed_at'] ? date('Y-m-d H:i', strtotime($req['reviewed_at'])) : '—' ?></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
