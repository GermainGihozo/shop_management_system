<?php
require '../includes/db.php';

// Get category ID and name from URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category_name = isset($_GET['name']) ? urldecode($_GET['name']) : '';

// If only category name is provided, try to find ID
if ($category_id == 0 && $category_name) {
    $stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
    $stmt->execute([$category_name]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($category) {
        $category_id = $category['id'];
    }
}

// Get category details
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found.");
}

// Get products in this category
$stmt = $conn->prepare("
    SELECT op.*, c.category_name 
    FROM online_products op
    LEFT JOIN categories c ON op.category_id = c.id
    WHERE op.category_id = ? 
    ORDER BY op.created_at DESC
");
$stmt->execute([$category_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($category['category_name']) ?> | HimShop</title>
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
    position: relative;
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
/* Category header */
.category-header {
    border-bottom: 2px solid #ffc107;
    padding-bottom: 15px;
    margin-bottom: 25px;
}
/* Discount badge */
.discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    z-index: 1;
}
/* Category badge */
.category-badge {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
}
/* Product count */
.product-count {
    color: #aaa;
    font-size: 0.9rem;
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
/* Empty state */
.empty-state {
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container">
        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
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
                <a href="../index.php" class="btn btn-outline-light btn-sm me-2 mt-2 mt-lg-0">← Back to Home</a>
                <a href="login.php" class="btn btn-warning btn-sm px-3 mt-2 mt-lg-0">Login</a>
            </div>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container py-4">
    <div class="category-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="text-warning fw-bold mb-1"><?= htmlspecialchars($category['category_name']) ?></h2>
                <p class="product-count mb-0">
                    <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> available
                </p>
            </div>
            <a href="../index.php" class="btn btn-outline-warning btn-sm mt-2 mt-md-0">
                Browse All Categories
            </a>
        </div>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <h4 class="text-muted">No products found in this category</h4>
            <p class="text-muted mb-3">Check back soon for new products!</p>
            <a href="../index.php" class="btn btn-warning">Return to Home</a>
        </div>
    <?php else: ?>
        <div class="row g-3 mt-3">
            <?php foreach ($products as $p): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="product_details.php?id=<?= $p['id'] ?>" class="text-decoration-none text-white">
                    <div class="product-card">
                        <?php if ($p['is_new'] == 1): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">NEW</span>
                        <?php endif; ?>
                        
                        <?php if ($p['discount'] > 0): ?>
                            <span class="discount-badge">-<?= $p['discount'] ?>%</span>
                        <?php endif; ?>

                        <img src="uploads/<?= htmlspecialchars($p['image']) ?>" 
                             class="product-img" 
                             alt="<?= htmlspecialchars($p['name']) ?>">
                        
                        <h6 class="mt-2 text-truncate"><?= htmlspecialchars($p['name']) ?></h6>
                        
                        <?php if ($p['discount'] > 0): ?>
                            <?php 
                            $discounted_price = $p['price'] * (1 - ($p['discount'] / 100));
                            ?>
                            <div>
                                <small class="text-muted text-decoration-line-through d-block">
                                    RWF <?= number_format($p['price'], 0) ?>
                                </small>
                                <small class="text-warning fw-bold">
                                    RWF <?= number_format($discounted_price, 0) ?>
                                </small>
                            </div>
                        <?php else: ?>
                            <small class="text-warning fw-bold">
                                RWF <?= number_format($p['price'], 0) ?>
                            </small>
                        <?php endif; ?>
                        
                        <?php if (!empty($p['category_name'])): ?>
                            <div class="mt-2">
                                <small class="category-badge"><?= htmlspecialchars($p['category_name']) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Products count footer -->
        <div class="mt-4 pt-3 border-top border-secondary text-center">
            <p class="text-muted">
                Showing <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> in 
                <span class="text-warning"><?= htmlspecialchars($category['category_name']) ?></span>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/index_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>