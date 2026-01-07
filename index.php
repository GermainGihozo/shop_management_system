<?php
require 'includes/db.php';

// Get categories with their products
$categories = $conn->query("
    SELECT c.id, c.category_name, 
           COUNT(op.id) as product_count
    FROM categories c
    LEFT JOIN online_products op ON c.id = op.category_id
    WHERE op.id IS NOT NULL
    GROUP BY c.id, c.category_name
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
        background: #111;
        color: #fff;
        padding-top: 56px; /* For fixed navbar */
    }
    .product-card {
        background: #1a1a1a;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        transition: 0.2s;
        position: relative;
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
    /* Floating WhatsApp Button */
    .whatsapp-float {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 60px;
        height: 60px;
        background: #25D366;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 9999;
        transition: 0.3s;
        animation: bounce 2s infinite;
    }
    .whatsapp-float:hover {
        transform: scale(1.1);
        background: #1ebe5d;
    }
    /* Bouncing animation */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
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
<?php include "includes/navbar.php"; ?>

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
                        <a href="online_shop/product_details.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:white;">
                            <div class="product-card">
                                <?php if ($p['is_new'] == 1): ?>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">NEW</span>
                                <?php endif; ?>
                                
                                <?php if ($p['discount'] > 0): ?>
                                    <span class="discount-badge">-<?= $p['discount'] ?>%</span>
                                <?php endif; ?>

                                <img src="online_shop/uploads/<?= htmlspecialchars($p['image']) ?>" 
                                     class="product-img" 
                                     alt="<?= htmlspecialchars($p['name']) ?>">
                                     
                                <h6 class="mt-2"><?= htmlspecialchars($p['name']) ?></h6>
                                
                                <?php if ($p['discount'] > 0): ?>
                                    <?php 
                                    $discounted_price = $p['price'] * (1 - ($p['discount'] / 100));
                                    ?>
                                    <div>
                                        <small class="text-muted text-decoration-line-through">
                                            RWF <?= number_format($p['price'], 0) ?>
                                        </small><br>
                                        <small class="text-warning fw-bold">
                                            RWF <?= number_format($discounted_price, 0) ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <small class="text-warning">RWF <?= number_format($p['price'], 0) ?></small>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <a href="online_shop/view_category.php?id=<?= $cat['id'] ?>&name=<?= urlencode($cat['category_name']) ?>" 
               class="btn btn-warning btn-sm mt-2">
                See more <?= htmlspecialchars($cat['category_name']) ?> →
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include "includes/index_footer.php"; ?>

<script>
// LIVE SEARCH FILTER
document.getElementById("search").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();
    
    document.querySelectorAll(".product-item").forEach(item => {
        let name = item.getAttribute("data-name");
        let category = item.getAttribute("data-category");
        
        if (name.includes(value) || category.includes(value)) {
            item.style.display = "";
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
            let nextBtn = group.nextElementSibling;
            if (nextBtn && nextBtn.tagName === 'A' && nextBtn.classList.contains('btn')) {
                nextBtn.style.display = "none";
            }
        } else {
            group.style.display = "";
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
    }, { threshold: 0.1 });

    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.add('fade-in');
        observer.observe(card);
    });
});
</script>

<a href="https://wa.me/250782823729" class="whatsapp-float" target="_blank">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16">
        <path d="M13.601 2.326A7.854 7.854 0 0 0 8.002.002C3.582.002.002 3.582.002 8c0 1.41.368 2.79 1.064 4.02L0 16l4.127-1.042A7.95 7.95 0 0 0 8 16c4.418 0 8-3.582 8-8 0-2.137-.833-4.146-2.399-5.674zM8 14.5a6.48 6.48 0 0 1-3.354-.92l-.24-.145-2.446.618.652-2.382-.157-.245A6.486 6.486 0 0 1 1.5 8c0-3.584 2.915-6.5 6.5-6.5 1.737 0 3.37.676 4.598 1.902A6.48 6.48 0 0 1 14.5 8c0 3.585-2.916 6.5-6.5 6.5zm3.538-4.365c-.198-.099-1.174-.578-1.356-.646-.182-.066-.315-.099-.448.1-.132.198-.514.646-.63.778-.116.133-.232.15-.43.05-.198-.1-.837-.308-1.594-.983-.59-.525-.99-1.175-1.106-1.373-.116-.198-.013-.304.087-.403.089-.088.198-.232.297-.348.1-.116.132-.198.198-.331.066-.132.033-.248-.017-.347-.05-.099-.448-1.078-.614-1.478-.162-.389-.328-.336-.448-.342l-.382-.007c-.132 0-.348.05-.53.248-.182.198-.695.68-.695 1.66 0 .98.712 1.926.811 2.059.1.132 1.402 2.137 3.396 2.996.475.205.845.327 1.133.418.476.151.909.13 1.252.079.383-.057 1.174-.48 1.34-.944.165-.464.165-.862.116-.944-.05-.083-.182-.132-.38-.231z"/>
    </svg>
</a>

</body>
</html>