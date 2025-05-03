<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$message = "";

// Get all products for the branch
$stmt = $conn->prepare("SELECT id, name, quantity, price FROM products WHERE branch_id = ?");
$stmt->execute([$branch_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle sale form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity_sold = intval($_POST['quantity']);

    // Fetch selected product details
    $stmt = $conn->prepare("SELECT quantity, price FROM products WHERE id = ? AND branch_id = ?");
    $stmt->execute([$product_id, $branch_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if ($product['quantity'] >= $quantity_sold && $quantity_sold > 0) {
            $price_each = $product['price'];
            $total_price = $price_each * $quantity_sold;

            // Update stock
            $update = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $update->execute([$quantity_sold, $product_id]);

            // Record sale
            $insert = $conn->prepare("INSERT INTO sales (product_id, branch_id, quantity, price_each, total_price, sold_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $insert->execute([$product_id, $branch_id, $quantity_sold, $price_each, $total_price]);

            $message = "<div class='alert alert-success'>✅ Igicuruzwa cyinjijwemo!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Stock ntihagije cg Igicuruzwa sicyo.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>⚠️ Igicuruzwa ntikibonetse</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Record Sale</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    @media (max-width: 576px) {
      h4 {
        font-size: 1.2rem;
      }
      .btn {
        width: 100%;
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
  <h4 class="mb-3">🧾 Injiza icyo ucuruje</h4>
  <?= $message ?>

  <form method="POST" class="p-4 bg-white rounded shadow-sm">
    <div class="mb-3">
      <label class="form-label">Hitamo Igicuruzwa</label>
      <select name="product_id" class="form-select" required>
        <option value="">-- Hitamo Igicuruzwa --</option>
        <?php foreach ($products as $product): ?>
          <option value="<?= $product['id'] ?>">
            <?= htmlspecialchars($product['name']) ?> (Stock: <?= $product['quantity'] ?> | RWF <?= number_format($product['price'], 2) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Ingano yacurujwe</label>
      <input type="number" name="quantity" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Itariki</label>
      <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s') ?>" readonly>
    </div>

    <div class="d-flex flex-column flex-md-row gap-2">
      <button type="submit" class="btn btn-success">💾 Emeza</button>
      <a href="dashboard.php" class="btn btn-secondary">← Subira ahabanza</a>
    </div>
  </form>
</div>
<?php
include'../includes/footer.php';
?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
