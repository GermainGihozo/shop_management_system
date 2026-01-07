<?php
require '../includes/db.php';
session_start(); // Add session start

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: products.php");
    exit;
}

$id          = intval($_POST['id']);
$name        = trim($_POST['name']);
$price       = floatval($_POST['price']);
$discount    = floatval($_POST['discount'] ?? 0);
$description = trim($_POST['description']);
$category_name = trim($_POST['category_name']);
$category_id = $_POST['category_id'];
$is_new      = intval($_POST['is_new'] ?? 0);

if (!$id || !$name || $price <= 0 || empty($category_name)) {
    die("Invalid input data.");
}

/* ==========================
   HANDLE CATEGORY
========================== */
if ($category_id === 'new' || empty($category_id)) {
    // Check if category already exists (case-insensitive)
    $stmt = $conn->prepare("SELECT id FROM categories WHERE LOWER(category_name) = LOWER(?)");
    $stmt->execute([$category_name]);
    $existingCat = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCat) {
        $category_id = $existingCat['id'];
    } else {
        // Create new category
        $stmt = $conn->prepare("INSERT INTO categories (category_name, created_at) VALUES (?, NOW())");
        $stmt->execute([$category_name]);
        $category_id = $conn->lastInsertId();
        
        // Log category creation to category_actions table
        if (isset($_SESSION['admin_id'])) {
            $admin_id = $_SESSION['admin_id'];
            $logStmt = $conn->prepare("
                INSERT INTO category_actions 
                (category_id, admin_id, action_type, created_at) 
                VALUES (?, ?, 'added', NOW())
            ");
            $logStmt->execute([$category_id, $admin_id]);
        }
    }
} else {
    $category_id = intval($category_id);
}

/* ==========================
   HANDLE IMAGE UPLOAD
========================== */
$imageSql = "";
$params = [$name, $price, $discount, $description, $category_id, $is_new];

// Check if new image is being uploaded
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Get current image name first
    $stmt = $conn->prepare("SELECT image FROM online_products WHERE id = ?");
    $stmt->execute([$id]);
    $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentImage = $currentProduct['image'] ?? null;
    
    // Delete old image if exists
    if ($currentImage && file_exists("uploads/$currentImage")) {
        unlink("uploads/$currentImage");
    }
    
    // Upload new image
    $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
    $targetPath = "uploads/" . $imageName;
    
    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imageSql = ", image = ?";
            $params[] = $imageName;
        }
    } else {
        die("Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.");
    }
}

$params[] = $id;

/* ==========================
   UPDATE PRODUCT
========================== */
$sql = "
    UPDATE online_products
    SET name = ?, price = ?, discount = ?, description = ?, 
        category_id = ?, is_new = ?
    $imageSql
    WHERE id = ?
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    // Success - redirect
    header("Location: products.php?msg=updated");
    exit;
    
} catch (PDOException $e) {
    die("Error updating product: " . $e->getMessage());
}