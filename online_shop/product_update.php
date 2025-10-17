<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $description = $_POST['description'];

    // Fetch current image
    $stmt = $conn->prepare("SELECT image FROM online_products WHERE id = ?");
    $stmt->execute([$id]);
    $oldImage = $stmt->fetchColumn();

    // Handle image upload
    $imageName = $oldImage;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/online_products/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Delete old image if exists
            if (!empty($oldImage) && file_exists($targetDir . $oldImage)) {
                unlink($targetDir . $oldImage);
            }
        } else {
            $imageName = $oldImage;
        }
    }

    $stmt = $conn->prepare("UPDATE online_products SET name=?, price=?, discount=?, description=?, image=? WHERE id=?");
    $stmt->execute([$name, $price, $discount, $description, $imageName, $id]);

    header("Location: products.php?updated=1");
    exit;
}
?>
