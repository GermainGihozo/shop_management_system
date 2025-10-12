<?php
require '../includes/db.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM online_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die("Product not found");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-5">
                <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded" alt="">
            </div>
            <div class="col-md-7">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <h4 class="text-warning">$<?= $product['price'] ?></h4>
                <?php if ($product['discount'] > 0): ?>
                    <p class="text-success">Discount: <?= $product['discount'] ?>%</p>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-light mt-3">← Back</a>
            </div>
        </div>
    </div>
</body>
</html>
