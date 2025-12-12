<?php
require '../includes/db.php';

$cat = $_GET['cat'] ?? '';
if (!$cat) die("Category missing.");

$stmt = $conn->prepare("SELECT * FROM online_products WHERE category = ? ORDER BY id DESC");
$stmt->execute([$cat]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($cat) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { 
    background: #111;
    color: #fff;
    padding-top: 90px;
}

/* Product cards */
.product-card {
    background: #1a1a1a; 
    border-radius: 12px; 
    padding: 10px; 
    text-align:center; 
    transition: .25s ease;
}
.product-card:hover { 
    transform: translateY(-5px); 
    background:#222; 
}

/* Image should scale on mobile */
.product-img { 
    width:100%; 
    height:180px; 
    object-fit:cover; 
    border-radius:8px; 
}

@media (max-width: 768px) {
    .product-img {
        height:150px;
    }
    h2 {
        font-size: 1.4rem !important;
    }
}

/* Fix navbar on mobile */
.navbar-brand span {
    font-size: 1.2rem;
}

/* Grid fix for very small screens */
@media (max-width: 480px) {
    .col-6 {
        padding: 6px;
    }
    .product-card {
        padding: 8px;
    }
}
</style>

</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../includes/images/logo.jpg" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
            <span class="fw-bold text-warning">HimShop</span>
        </a>

        <!-- Mobile toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- NAV CONTENT -->
        <div class="collapse navbar-collapse" id="mainNav">

            <div class="ms-auto">
                <a href="login.php" class="btn btn-warning btn-sm px-3 mt-2 mt-lg-0">Login</a>
            </div>

        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container py-4">
    <h2 class="text-warning fw-bold"><?= htmlspecialchars($cat) ?></h2>
    <div class="row g-3 mt-3">

        <?php foreach ($products as $p): ?>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="product_details.php?id=<?= $p['id'] ?>" class="text-decoration-none text-white">
                <div class="product-card">
                    <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="product-img">
                    <h6 class="mt-2 text-truncate"><?= htmlspecialchars($p['name']) ?></h6>

                    <small class="text-warning">
                        RWF <?= number_format($p['price']) ?>
                    </small>
                </div>
            </a>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<?php include '../includes/index_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
