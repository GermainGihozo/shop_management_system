<?php
require '../includes/db.php';

// Get product ID from URL
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = $_GET['id'];

// Fetch product
$stmt = $conn->prepare("SELECT * FROM online_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Fetch all categories (distinct)
$catQuery = $conn->query("SELECT DISTINCT category FROM online_products ORDER BY category ASC");
$allCategories = $catQuery->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.autocomplete-box {
    position: absolute;
    background: white;
    width: 100%;
    z-index: 999;
    border: 1px solid #ccc;
    border-radius: 5px;
    max-height: 180px;
    overflow-y: auto;
    display: none;
}

.autocomplete-item {
    padding: 8px 10px;
    cursor: pointer;
}

.autocomplete-item:hover {
    background: #f1f1f1;
}
</style>

</head>
<body class="bg-light">
<?php include 'admin_navbar.php'; ?>

<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4>Edit Product - <?php echo htmlspecialchars($product['name']); ?></h4>
    </div>

    <div class="card-body">
      <form action="product_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text" name="name" class="form-control"
                 value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price (RWF)</label>
          <input type="number" name="price" class="form-control"
                 value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Discount (%)</label>
          <input type="number" name="discount" class="form-control"
                 value="<?php echo $product['discount']; ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?php
            echo htmlspecialchars($product['description']);
          ?></textarea>
        </div>

        <div class="mb-3 position-relative">
          <label class="form-label">Category</label>
          <input type="text" id="categoryInput" name="category"
                 class="form-control"
                 value="<?php echo htmlspecialchars($product['category']); ?>">

          <!-- Autocomplete Area -->
          <div id="categoryList" class="autocomplete-box"></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Product Image</label><br>
          <img src="uploads/<?php echo $product['image']; ?>" width="120" class="mb-2 rounded">
          <input type="file" name="image" class="form-control">
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success">Update</button>
          <a href="products.php" class="btn btn-secondary">Cancel</a>
        </div>

      </form>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Autocomplete Categories
let categories = <?php echo json_encode($allCategories); ?>;
let input = document.getElementById("categoryInput");
let box = document.getElementById("categoryList");

input.addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    box.innerHTML = "";

    if (value.length === 0) {
        box.style.display = "none";
        return;
    }

    let filtered = categories.filter(cat =>
        cat.toLowerCase().includes(value)
    );

    if (filtered.length === 0) {
        box.style.display = "none";
        return;
    }

    filtered.forEach(cat => {
        let item = document.createElement("div");
        item.classList.add("autocomplete-item");
        item.innerText = cat;

        item.onclick = function () {
            input.value = this.innerText;
            box.style.display = "none";
        };

        box.appendChild(item);
    });

    box.style.display = "block";
});
</script>

</body>
</html>
