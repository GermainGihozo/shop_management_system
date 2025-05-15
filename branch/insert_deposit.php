<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('branch');

$branch_id = $_SESSION['branch_id'];
$msg = "";

// Check if today's deposit already exists
$check_stmt = $conn->prepare("SELECT * FROM deposits WHERE branch_id = ? AND deposit_date = CURDATE()");
$check_stmt->execute([$branch_id]);
$today_deposit = $check_stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$today_deposit) {
    $amount = $_POST['amount'];
    $insert_stmt = $conn->prepare("INSERT INTO deposits (branch_id, amount, deposit_date) VALUES (?, ?, CURDATE())");
    $insert_stmt->execute([$branch_id, $amount]);
    $msg = "✅ Deposit recorded successfully.";
    header("Refresh:1");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Insert Daily Deposit</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .deposit-box {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <div class="deposit-box">
    <h4 class="mb-4 text-center">💵 Insert Today's Deposit</h4>

    <?php if ($msg): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if ($today_deposit): ?>
      <div class="alert alert-info">
        ✅ Today's deposit is already recorded: <strong><?= number_format($today_deposit['amount'], 2) ?> RWF</strong>
      </div>
    <?php else: ?>
      <form method="POST">
        <div class="mb-3">
          <label for="amount" class="form-label">Amount (RWF)</label>
          <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Submit Deposit</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php 
include "../includes/footer.php";
?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
