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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #111;
     color: #fff;
    padding-top: 95px;
    }
.product-card {
    background: #1a1a1a; border-radius: 10px; padding: 10px; text-align:center; transition:.2s;
}
.product-img { width:100%; height:200px; object-fit:cover; border-radius:8px; }
.product-card:hover { transform:scale(1.05); background:#222; }
</style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
             <img src="../includes/images/logo.jpg" alt="Logo" width="40" height="40" class="me-2">
            <span class="fw-bold text-warning">HimShop</span>
        </a>

        <!-- TOGGLER FOR MOBILE -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- NAV ITEMS -->
        <div class="collapse navbar-collapse" id="mainNav">

            <!-- CENTER SEARCH BAR -->
            <!-- <form class="d-flex mx-auto w-50" action="search.php" method="GET">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Search for products...">
            </form> -->

            <!-- RIGHT SIDE BUTTONS -->
            <div class="d-flex ms-auto">
                <a href="login.php" class="btn btn-warning btn-sm px-3">Login</a>
            </div>

        </div>

    </div>
</nav>

<div class="container py-4">
    <h2 class="text-warning"><?= htmlspecialchars($cat) ?></h2>
    <div class="row g-3 mt-3">

        <?php foreach ($products as $p): ?>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="product_details.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:white;">
                <div class="product-card">
                    <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="product-img">
                    <h6 class="mt-2"><?= htmlspecialchars($p['name']) ?></h6>
                    <small class="text-warning">RWF <?= number_format($p['price']) ?></small>
                </div>
            </a>
        </div>
        <?php endforeach; ?>

    </div>
</div>
<?php
include '../includes/index_footer.php';
?>
</body>
</html>
