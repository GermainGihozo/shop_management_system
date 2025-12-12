<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $discount = $_POST['discount'];
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

// $stmt = $conn->prepare("INSERT INTO online_products (name, category, price, discount, description, image) 
// VALUES (?, ?, ?, ?, ?, ?)");
// $stmt->execute([$name, $category, $price, $discount, $description, $imageName]);

    $stmt = $conn->prepare("INSERT INTO online_products (name, category, description, price, discount, image, is_new) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category, $desc, $price, $discount, $image, $is_new]);
    // Insert category if it doesn't exist
$catStmt = $conn->prepare("INSERT IGNORE INTO categories(category_name) VALUES(?)");
$catStmt->execute([$category]);


    echo "<script>alert('Product added successfully!'); window.location='admin_add_product.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

</head>
<body class="bg-dark text-light">
    <?php
require 'admin_navbar.php';
    ?>
    <div class="container py-5">
        <h3>Add New Product</h3>
        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description:</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label>Price:</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Discount (%):</label>
                <input type="number" step="0.01" name="discount" class="form-control">
            </div>
            <div class="mb-3">
                <label>Product Image:</label>
                <input type="file" name="image" class="form-control" required>
            </div>
      <div class="mb-3">
    <label class="form-label">Category</label>
    <input name="category" id="categoryInput" class="form-control">
</div>



            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_new" id="is_new" checked>
                <label class="form-check-label" for="is_new">Mark as New Product</label>
            </div>
            <button class="btn btn-warning mt-3">Save Product</button>
        </form>
       <a href="products.php"><button class="btn btn-warning mt-3">View Products</button></a>
    </div>
    <?php
    include '../includes/footer.php';
    
    ?>
    <script>
    fetch('get_categories.php')
        .then(res => res.json())
        .then(data => {
            let input = document.querySelector("#categoryInput");
            let tagify = new Tagify(input, {
                whitelist: data,
                dropdown: {
                    enabled: 1,
                    maxItems: 5,
                    classname: "tags-look"
                }
            });
        });
</script>

</body>
</html>
