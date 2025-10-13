<?php
require '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch and delete image
    $stmt = $conn->prepare("SELECT image FROM online_products WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    if ($image && file_exists("../uploads/online_products/" . $image)) {
        unlink("../uploads/online_products/" . $image);
    }

    // Delete record
    $stmt = $conn->prepare("DELETE FROM online_products WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: products.php?deleted=1");
    exit;
}
?>
