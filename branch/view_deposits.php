<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('branch');

$branch_id = $_SESSION['branch_id'];

$stmt = $conn->prepare("SELECT * FROM deposits WHERE branch_id = ? ORDER BY deposit_date DESC");
$stmt->execute([$branch_id]);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Deposits</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h4>📅 Daily Deposits</h4>

  <?php if (empty($deposits)): ?>
    <div class="alert alert-info">No deposits found.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered mt-3">
        <thead class="table-dark">
          <tr>
            <th>Date</th>
            <th>Amount (RWF)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($deposits as $deposit): ?>
            <tr>
              <td><?= htmlspecialchars($deposit['deposit_date']) ?></td>
              <td><?= number_format($deposit['amount'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
