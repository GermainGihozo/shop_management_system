<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_name = trim($_POST['category']);
    $discount = floatval($_POST['discount'] ?? 0);
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    // Validate inputs
    if (empty($name) || $price <= 0 || empty($category_name)) {
        echo "<script>alert('Please fill all required fields!'); window.location='admin_add_product.php';</script>";
        exit;
    }

    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
        $targetPath = "uploads/" . $image;
        
        // Validate image type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.'); window.location='admin_add_product.php';</script>";
            exit;
        }
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            echo "<script>alert('Failed to upload image.'); window.location='admin_add_product.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Product image is required!'); window.location='admin_add_product.php';</script>";
        exit;
    }

    // Handle category - check if exists or create new
    $catStmt = $conn->prepare("SELECT id FROM categories WHERE LOWER(category_name) = LOWER(?)");
    $catStmt->execute([$category_name]);
    $existingCat = $catStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCat) {
        $category_id = $existingCat['id'];
    } else {
        // Create new category
        $catStmt = $conn->prepare("INSERT INTO categories (category_name, created_at) VALUES (?, NOW())");
        $catStmt->execute([$category_name]);
        $category_id = $conn->lastInsertId();
        
        // Log category creation (optional - requires session)
        session_start();
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $logStmt = $conn->prepare("
                INSERT INTO category_actions (category_id, admin_id, action_type, created_at) 
                VALUES (?, ?, 'added', NOW())
            ");
            $logStmt->execute([$category_id, $admin_id]);
        }
    }

    // Insert product with category_id
    $stmt = $conn->prepare("
        INSERT INTO online_products 
        (name, category_id, description, price, discount, image, is_new, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$name, $category_id, $desc, $price, $discount, $image, $is_new])) {
        echo "<script>alert('Product added successfully!'); window.location='products.php';</script>";
    } else {
        echo "<script>alert('Error adding product!'); window.location='admin_add_product.php';</script>";
    }
    exit;
}

// Get existing categories for autocomplete
$catQuery = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
$existingCategories = $catQuery->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
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
            color: #000;
        }
        .autocomplete-item:hover {
            background: #f1f1f1;
        }
        body {
            background-color: #121212;
            color: #fff;
        }
        .form-control, .form-control:focus {
            background-color: #1e1e1e;
            color: #fff;
            border-color: #444;
        }
        .form-label {
            color: #ddd;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <?php require 'admin_navbar.php'; ?>
    
    <div class="container py-5">
        <h3>Add New Product</h3>
        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price (RWF):</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Discount (%):</label>
                <input type="number" step="0.01" name="discount" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Product Image:</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
                <small class="text-muted">Supported: JPG, PNG, GIF, WebP</small>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Category:</label>
                <input type="text" 
                       name="category" 
                       id="categoryInput" 
                       class="form-control" 
                       autocomplete="off"
                       required>
                <div id="categoryList" class="autocomplete-box"></div>
                <small class="text-muted">Type to select existing category or enter new one</small>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_new" id="is_new" checked>
                <label class="form-check-label" for="is_new">Mark as New Product</label>
            </div>
            <button type="submit" class="btn btn-warning mt-3">Save Product</button>
            <a href="products.php" class="btn btn-secondary mt-3">View Products</a>
        </form>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
    // Autocomplete functionality
    let categories = <?= json_encode($existingCategories) ?>;
    let input = document.getElementById("categoryInput");
    let box = document.getElementById("categoryList");
    
    input.addEventListener("keyup", () => {
        let value = input.value.toLowerCase();
        box.innerHTML = "";
        
        if (!value) {
            box.style.display = "none";
            return;
        }
        
        let filtered = categories.filter(cat =>
            cat.toLowerCase().includes(value)
        );
        
        // Add "Create new" option
        filtered.push(`Create new: "${value}"`);
        
        filtered.forEach(cat => {
            let div = document.createElement("div");
            div.className = "autocomplete-item";
            div.textContent = cat;
            div.onclick = () => {
                if (cat.startsWith("Create new:")) {
                    input.value = value;
                } else {
                    input.value = cat;
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