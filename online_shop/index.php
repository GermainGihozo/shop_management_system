<?php
require '../includes/db.php';

// Get categories with their products count
$categories = $conn->query("
    SELECT c.id, c.category_name, 
           COUNT(op.id) as product_count
    FROM categories c
    LEFT JOIN online_products op ON c.id = op.category_id
    GROUP BY c.id, c.category_name
    HAVING product_count > 0
    ORDER BY c.category_name ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shop | Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
    body {
        padding-top: 75px;
        background: #111;
        color: #fff;
    }
    .product-card {
        background: #1a1a1a;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        transition: 0.2s;
        position: relative;
        overflow: hidden;
    }
    .product-card:hover {
        transform: scale(1.05);
        background: #222;
    }
    .product-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
    }
    .category-title {
        border-left: 4px solid #ffc107;
        padding-left: 10px;
        margin-bottom: 15px;
    }
    .category-badge {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        margin-left: 8px;
    }
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: all .6s ease-in-out;
    }
    .fade-in.show {
        opacity: 1;
        transform: translateY(0);
    }
    .carousel-item {
        padding: 15px;
    }
    .product-card img {
        height: 200px;
        object-fit: cover;
    }
    .social-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transition: 0.3s;
    }

    .social-icon:hover {
        background: #0d6efd;
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .footer-link {
        color: #aaa;
        text-decoration: none;
        display: block;
        margin-bottom: 6px;
        transition: 0.3s;
    }

    .footer-link:hover {
        color: #fff;
        padding-left: 4px;
    }
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
            <!-- RIGHT SIDE BUTTONS -->
            <div class="d-flex ms-auto">
                <a href="login.php" class="btn btn-warning btn-sm px-3">Login</a>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- SEARCH BAR -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <input type="text" id="search" class="form-control form-control-lg" placeholder="Search products...">
        </div>
    </div>

    <?php if (empty($categories)): ?>
        <div class="alert alert-info text-center">
            No categories available yet. Products will appear here once added.
        </div>
    <?php else: ?>
        <?php foreach ($categories as $cat): ?>
            <h3 class="category-title mt-4">
                <?= htmlspecialchars($cat['category_name']) ?>
                <span class="category-badge"><?= $cat['product_count'] ?> items</span>
            </h3>

            <div class="row g-3 product-group" data-category="<?= htmlspecialchars(strtolower($cat['category_name'])) ?>">

                <?php
                $stmt = $conn->prepare("
                    SELECT op.*, c.category_name 
                    FROM online_products op
                    LEFT JOIN categories c ON op.category_id = c.id
                    WHERE op.category_id = ? 
                    ORDER BY op.created_at DESC 
                    LIMIT 6
                ");
                $stmt->execute([$cat['id']]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($products as $p): ?>
                    <div class="col-6 col-md-4 col-lg-2 product-item" 
                         data-name="<?= strtolower($p['name']) ?>"
                         data-category="<?= strtolower($p['category_name']) ?>">
                        <a href="product_details.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:white;">
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
                                     
                                <h6 class="mt-2"><?= htmlspecialchars($p['name']) ?></h6>
                                
                                <?php if ($p['discount'] > 0): ?>
                                    <?php 
                                    $discounted_price = $p['price'] * (1 - ($p['discount'] / 100));
                                    ?>
                                    <div>
                                        <small class="text-muted text-decoration-line-through">
                                            RWF <?= number_format($p['price'], 2) ?>
                                        </small><br>
                                        <small class="text-warning fw-bold">
                                            RWF <?= number_format($discounted_price, 2) ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <small class="text-warning">RWF <?= number_format($p['price'], 2) ?></small>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

            </div>

            <a href="view_category.php?id=<?= $cat['id'] ?>&name=<?= urlencode($cat['category_name']) ?>" 
               class="btn btn-warning btn-sm mt-2">
                See more <?= htmlspecialchars($cat['category_name']) ?> →
            </a>

        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php
include "../includes/index_footer.php";
?>

<script>
// LIVE SEARCH FILTER
document.getElementById("search").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    
    // Hide/show products
    document.querySelectorAll(".product-item").forEach(item => {
        let name = item.getAttribute("data-name");
        let category = item.getAttribute("data-category");
        
        // Show if matches product name OR category
        if (name.includes(value) || category.includes(value)) {
            item.style.display = "";
            // Also show the parent category group if hidden
            let group = item.closest('.product-group');
            group.style.display = "";
        } else {
            item.style.display = "none";
        }
    });
    
    // Hide empty category groups
    document.querySelectorAll(".product-group").forEach(group => {
        let visibleItems = group.querySelectorAll('.product-item[style=""]').length;
        if (visibleItems === 0) {
            group.style.display = "none";
            // Also hide the "See more" button
            let nextBtn = group.nextElementSibling;
            if (nextBtn && nextBtn.tagName === 'A' && nextBtn.classList.contains('btn')) {
                nextBtn.style.display = "none";
            }
        } else {
            group.style.display = "";
            // Show the "See more" button
            let nextBtn = group.nextElementSibling;
            if (nextBtn && nextBtn.tagName === 'A' && nextBtn.classList.contains('btn')) {
                nextBtn.style.display = "";
            }
        }
    });
});

// Intersection Observer for fade-in animation
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('show');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.add('fade-in');
        observer.observe(card);
    });
});
</script>
</body>
</html>