<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('branch');

// Get current branch ID
$branch_id = $_SESSION['branch_id'];

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $request_id = $_POST['request_id'];
  $action = $_POST['action'];

  // Fetch the request
  $stmt = $conn->prepare("SELECT * FROM product_update_requests WHERE id = ? AND branch_id = ?");
  $stmt->execute([$request_id, $branch_id]);
  $request = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($request && $request['status'] === 'pending') {
    if ($action === 'approve') {
      $conn->beginTransaction();

      $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?")
           ->execute([$request['added_quantity'], $request['product_id']]);

      $conn->prepare("
        UPDATE product_update_requests 
        SET status = 'approved', reviewed_at = NOW(), reviewed_by_branch_user_id = ? 
        WHERE id = ?
      ")->execute([$_SESSION['user_id'], $request_id]);

      $conn->commit();
    } elseif ($action === 'reject') {
      $conn->prepare("
        UPDATE product_update_requests 
        SET status = 'rejected', reviewed_at = NOW(), reviewed_by_branch_user_id = ? 
        WHERE id = ?
      ")->execute([$_SESSION['user_id'], $request_id]);
    }
  }

  header("Location: branch_quantity_requests.php");
  exit;
}

// Load pending requests
$stmt = $conn->prepare("
  SELECT r.*, p.name AS product_name, u.username AS admin_username
  FROM product_update_requests r
  JOIN products p ON r.product_id = p.id
  JOIN users u ON r.requested_by_admin_id = u.id
  WHERE r.branch_id = ? AND r.status = 'pending'
  ORDER BY r.requested_at DESC
");
$stmt->execute([$branch_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pending Quantity Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .container {
      flex: 1;
    }
    @media (max-width: 576px) {
      h4 {
        font-size: 1.25rem;
      }
      .btn {
        margin-bottom: 4px;
      }
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <h4 class="mb-3">🔄 Pending Quantity Requests</h4>

  <?php if (empty($requests)): ?>
    <div class="alert alert-info">No pending requests.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>Product</th>
            <th>Requested By</th>
            <th>Quantity to Add</th>
            <th>Requested At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $req): ?>
          <tr>
            <td><?= htmlspecialchars($req['product_name']) ?></td>
            <td><?= htmlspecialchars($req['admin_username']) ?></td>
            <td><?= $req['added_quantity'] ?></td>
            <td><?= date('Y-m-d H:i', strtotime($req['requested_at'])) ?></td>
            <td>
              <form method="POST" class="d-flex flex-wrap gap-2">
                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                <button name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
