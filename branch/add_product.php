<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if ($name && $price > 0 && $quantity >= 0) {

        // Check duplicate product for the same branch
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ? AND branch_id = ?");
        $stmt->execute([$name, $branch_id]);

        if ($stmt->rowCount() > 0) {
            $message = "<div class='alert alert-warning'>🚫 Product already exists!</div>";
        } else {

            // Insert pending product
            $stmt = $conn->prepare("
                INSERT INTO products (name, price, quantity, branch_id, status)
                VALUES (?, ?, ?, ?, 'pending')
            ");

            if ($stmt->execute([$name, $price, $quantity, $branch_id])) {
                $message = "
                <div class='alert alert-success'>
                    ✅ Product submitted and is awaiting admin approval!
                </div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Failed to submit product.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>⚠️ Please fill all fields correctly.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    @media (max-width: 576px) {
      h4 { font-size: 1.2rem; }
      .btn { width: 100%; margin-bottom: 10px; }
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
  <h4 class="mb-3">➕ Add New Product</h4>
  <?= $message ?>

  <form method="POST" class="p-4 bg-white rounded shadow-sm">
    <div class="mb-3">
      <label class="form-label">Product Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Price (RWF)</label>
      <input type="number" step="0.01" name="price" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Quantity</label>
      <input type="number" name="quantity" class="form-control" required>
    </div>

    <div class="d-flex flex-column flex-md-row gap-2">
      <button type="submit" class="btn btn-primary">💾 Submit for Approval</button>
      <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
