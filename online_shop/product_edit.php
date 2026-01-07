<?php
require '../includes/db.php';

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = intval($_GET['id']);

/* ==========================
   FETCH PRODUCT WITH CATEGORY NAME
========================== */
$stmt = $conn->prepare("
    SELECT op.*, c.category_name 
    FROM online_products op
    LEFT JOIN categories c ON op.category_id = c.id
    WHERE op.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

/* ==========================
   FETCH ALL CATEGORIES FOR DROPDOWN
========================== */
$catQuery = $conn->query("
    SELECT id, category_name 
    FROM categories 
    ORDER BY category_name ASC
");
$allCategories = $catQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.autocomplete-box {
    position: absolute;
    background: #fff;
    width: 100%;
    border: 1px solid #ccc;
    z-index: 1000;
    max-height: 180px;
    overflow-y: auto;
    display: none;
}
.autocomplete-item {
    padding: 8px;
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
    <h4>Edit Product – <?= htmlspecialchars($product['name']) ?></h4>
</div>

<div class="card-body">
<form action="product_update.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $product['id'] ?>">

<div class="mb-3">
<label>Product Name</label>
<input type="text" name="name" class="form-control"
       value="<?= htmlspecialchars($product['name']) ?>" required>
</div>

<div class="mb-3">
<label>Price (RWF)</label>
<input type="number" step="0.01" name="price" class="form-control"
       value="<?= $product['price'] ?>" required>
</div>

<div class="mb-3">
<label>Discount (%)</label>
<input type="number" step="0.01" name="discount" class="form-control"
       value="<?= $product['discount'] ?>">
</div>

<div class="mb-3">
<label>Description</label>
<textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
</div>

<div class="mb-3 position-relative">
<label>Category</label>
<!-- Hidden field for category_id -->
<input type="hidden" id="category_id" name="category_id" 
       value="<?= $product['category_id'] ?>">
       
<!-- Visible field for category name -->
<input type="text"
       id="categoryInput"
       name="category_name"
       class="form-control"
       value="<?= htmlspecialchars($product['category_name'] ?? '') ?>"
       autocomplete="off">

<div id="categoryList" class="autocomplete-box"></div>
<small class="text-muted">Type to search existing categories or enter a new one</small>
</div>

<!-- Existing categories for autocomplete -->
<select id="existingCategories" class="d-none">
    <option value="">Select Category</option>
    <?php foreach ($allCategories as $cat): ?>
    <option value="<?= $cat['id'] ?>" 
            data-name="<?= htmlspecialchars($cat['category_name']) ?>">
        <?= htmlspecialchars($cat['category_name']) ?>
    </option>
    <?php endforeach; ?>
</select>

<div class="mb-3">
<label>Mark as New Product?</label>
<select name="is_new" class="form-control">
    <option value="0" <?= $product['is_new'] == 0 ? 'selected' : '' ?>>No</option>
    <option value="1" <?= $product['is_new'] == 1 ? 'selected' : '' ?>>Yes</option>
</select>
</div>

<div class="mb-3">
<label>Image</label><br>
<?php if (!empty($product['image'])): ?>
<img src="uploads/<?= htmlspecialchars($product['image']) ?>" 
     width="120" class="mb-2 rounded">
<?php endif; ?>
<input type="file" name="image" class="form-control">
<small class="text-muted">Leave empty to keep current image</small>
</div>

<div class="text-end">
<button class="btn btn-success">Update</button>
<a href="products.php" class="btn btn-secondary">Cancel</a>
</div>

</form>
</div>
</div>
</div>

<script>
// Get categories from the hidden select
let categoryOptions = document.querySelectorAll('#existingCategories option');
let categories = [];
categoryOptions.forEach(option => {
    if (option.value) {
        categories.push({
            id: option.value,
            name: option.dataset.name
        });
    }
});

let input = document.getElementById("categoryInput");
let categoryIdInput = document.getElementById("category_id");
let box = document.getElementById("categoryList");

input.addEventListener("keyup", () => {
    let value = input.value.toLowerCase();
    box.innerHTML = "";

    if (!value) {
        box.style.display = "none";
        categoryIdInput.value = ""; // Clear category ID when input is empty
        return;
    }

    // Filter categories
    let filtered = categories.filter(cat =>
        cat.name.toLowerCase().includes(value)
    );

    // Add "Create new: [value]" option
    filtered.push({
        id: 'new',
        name: `Create new: "${value}"`
    });

    filtered.forEach(cat => {
        let div = document.createElement("div");
        div.className = "autocomplete-item";
        div.textContent = cat.name;
        div.dataset.id = cat.id;
        div.onclick = () => {
            if (cat.id === 'new') {
                // For new category, keep the text but clear the ID
                input.value = value;
                categoryIdInput.value = 'new';
            } else {
                // For existing category, set both name and ID
                input.value = cat.name;
                categoryIdInput.value = cat.id;
            }
            box.style.display = "none";
        };
        box.appendChild(div);
    });

    box.style.display = filtered.length ? "block" : "none";
});

// Hide autocomplete when clicking elsewhere
document.addEventListener('click', (e) => {
    if (!box.contains(e.target) && e.target !== input) {
        box.style.display = 'none';
    }
});
</script>

</body>
</html>