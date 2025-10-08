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

            $message = "<div class='alert alert-success'>✅ Sale recorded successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Not enough stock or invalid quantity.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>⚠️ Product not found!</div>";
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
  <h4 class="mb-3">🧾 Record Product Sale</h4>
  <?= $message ?>

  <form method="POST" class="p-4 bg-white rounded shadow-sm">
    <div class="mb-3 position-relative">
  <label class="form-label">Search Product</label>
  <input type="text" id="productSearch" class="form-control" placeholder="Type product name..." autocomplete="off">
  <div id="suggestions" class="list-group position-absolute w-100" style="z-index:1000;"></div>
</div>

<input type="hidden" name="product_id" id="product_id">

<div class="mb-3">
  <label class="form-label">Product Price</label>
  <input type="text" id="price" class="form-control" readonly>
</div>

<div class="mb-3">
  <label class="form-label">Available Stock</label>
  <input type="text" id="stock" class="form-control" readonly>
</div>

<div class="mb-3">
  <label class="form-label">Quantity Sold</label>
  <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
</div>


    <div class="mb-3">
      <label class="form-label">Date of Sale</label>
      <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s') ?>" readonly>
    </div>

    <div class="d-flex flex-column flex-md-row gap-2">
      <button type="submit" class="btn btn-success">💾 Record Sale</button>
      <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
  </form>
</div>
<?php
include'../includes/footer.php';
?>
<script src="js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("productSearch");
  const suggestions = document.getElementById("suggestions");

  searchInput.addEventListener("input", async () => {
    const query = searchInput.value.trim();
    if (query.length < 2) {
      suggestions.innerHTML = "";
      return;
    }

    const response = await fetch(`search_product.php?query=${encodeURIComponent(query)}`);
    const products = await response.json();

    suggestions.innerHTML = "";
    products.forEach(p => {
      const item = document.createElement("a");
      item.href = "#";
      item.classList.add("list-group-item", "list-group-item-action");
      item.textContent = `${p.name} (RWF ${p.price} | Stock: ${p.quantity})`;

      item.onclick = e => {
        e.preventDefault();
        document.getElementById("product_id").value = p.id;
        document.getElementById("price").value = p.price;
        document.getElementById("stock").value = p.quantity;
        searchInput.value = p.name;
        suggestions.innerHTML = "";
      };

      suggestions.appendChild(item);
    });
  });
});
</script>

</body>
</html>
