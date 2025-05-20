<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('branch');

$branch_id = $_SESSION['branch_id'];

$stmt = $conn->prepare("
    SELECT r.*, p.name AS product_name 
    FROM product_update_requests r
    JOIN products p ON r.product_id = p.id
    WHERE r.branch_id = ? AND r.status = 'pending'
    ORDER BY r.requested_at DESC
");
$stmt->execute([$branch_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Quantity Update Requests</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <h4>📦 Quantity Update Requests</h4>

  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <?php if (count($requests) === 0): ?>
    <div class="alert alert-info">No pending requests.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Product</th>
            <th>Current Quantity</th>
            <th>Requested Quantity</th>
            <th>Requested At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $req): ?>
          <tr>
            <td><?= htmlspecialchars($req['product_name']) ?></td>
            <td>
              <?php
              // Fetch current quantity live
              $q_stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
              $q_stmt->execute([$req['product_id']]);
              $current = $q_stmt->fetchColumn();
              echo $current;
              ?>
            </td>
            <td><?= $req['new_quantity'] ?></td>
            <td><?= date('Y-m-d H:i', strtotime($req['requested_at'])) ?></td>
            <td>
              <form method="POST" action="process_request.php" style="display:inline-block;">
                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                <input type="hidden" name="action" value="approve">
                <button class="btn btn-success btn-sm" onclick="return confirm('Approve this request?')">Approve</button>
              </form>
              <form method="POST" action="process_request.php" style="display:inline-block;">
                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                <input type="hidden" name="action" value="reject">
                <button class="btn btn-danger btn-sm" onclick="return confirm('Reject this request?')">Reject</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
