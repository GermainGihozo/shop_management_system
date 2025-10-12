<?php
require '../includes/db.php';
$newProducts = $conn->query("SELECT * FROM online_products WHERE is_new = 1 ORDER BY created_at DESC")->fetchAll();
$otherProducts = $conn->query("SELECT * FROM online_products WHERE is_new = 0 ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Shop | Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container py-4">
        <h2 class="mb-4 text-center text-warning">🆕 New Arrivals</h2>
        <div class="row">
            <?php foreach ($newProducts as $p): ?>
            <div class="col-md-3 mb-4">
                <div class="card bg-light text-dark">
                    <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                    <div class="card-body text-center">
                        <h5><?= htmlspecialchars($p['name']) ?></h5>
                        <p>Price: <strong>$<?= $p['price'] ?></strong></p>
                        <?php if ($p['discount'] > 0): ?>
                            <span class="badge bg-success">-<?= $p['discount'] ?>%</span>
                        <?php endif; ?>
                        <a href="product_details.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning mt-2">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <h2 class="mt-5 mb-4 text-center text-info">🛍️ Other Products</h2>
        <div class="row">
            <?php foreach ($otherProducts as $p): ?>
            <div class="col-md-3 mb-4">
                <div class="card bg-light text-dark">
                    <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                    <div class="card-body text-center">
                        <h5><?= htmlspecialchars($p['name']) ?></h5>
                        <p>Price: <strong>$<?= $p['price'] ?></strong></p>
                        <a href="product_details.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info mt-2">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
