<?php
session_start();
require '../includes/db.php';
require_once 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];
$product_id = intval($_GET['id']);

// ✅ FIX: PDO product check
$check = $conn->prepare(
    "SELECT * FROM products 
     WHERE id = :id AND branch_id = :branch_id AND status = 'approved'"
);
$check->execute([
    ':id' => $product_id,
    ':branch_id' => $branch_id
]);

$product = $check->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found or not accessible.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sold_qty = intval($_POST['quantity']);

    if ($sold_qty > $product['quantity']) {
        die("Cannot sell more than available quantity.");
    }

    $new_quantity = $product['quantity'] - $sold_qty;
    $price_each = $product['price'];
    $total_price = $sold_qty * $price_each;

    // ✅ FIX: Update stock (PDO)
    $update = $conn->prepare(
        "UPDATE products SET quantity = :qty WHERE id = :id"
    );
    $update->execute([
        ':qty' => $new_quantity,
        ':id' => $product_id
    ]);

    // ✅ FIX: Insert sale (PDO)
    $insert = $conn->prepare(
        "INSERT INTO sales 
        (product_id, branch_id, quantity, price_each, total_price)
        VALUES (:product_id, :branch_id, :quantity, :price_each, :total_price)"
    );
    $insert->execute([
        ':product_id' => $product_id,
        ':branch_id' => $branch_id,
        ':quantity' => $sold_qty,
        ':price_each' => $price_each,
        ':total_price' => $total_price
    ]);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Sell Product</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h4>Sell Product: <?= htmlspecialchars($product['name']) ?></h4>
  <form method="POST">
    <div class="mb-3">
      <label>Quantity Sold (Available: <?= $product['quantity'] ?>)</label>
      <input type="number"
             name="quantity"
             class="form-control"
             required
             min="1"
             max="<?= $product['quantity'] ?>">
    </div>
    <button class="btn btn-danger">Sell</button>
  </form>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
