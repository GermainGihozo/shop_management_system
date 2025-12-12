<?php
require '../includes/db.php';

// Get unique categories
$categories = $conn->query("SELECT DISTINCT category FROM online_products ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
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
</style>
</head>
<body>
<?php
// include "../includes/navbar.php";
?>
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

<!-- FIX LAYOUT SPACING -->


<div class="container py-4">

    <!-- SEARCH BAR -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <input type="text" id="search" class="form-control form-control-lg" placeholder="Search products...">
        </div>
    </div>

    <?php foreach ($categories as $cat): ?>

        <h3 class="category-title mt-4"><?= htmlspecialchars($cat) ?> </h3>

        <div class="row g-3 product-group" data-category="<?= htmlspecialchars($cat) ?>">

            <?php
            $stmt = $conn->prepare("SELECT * FROM online_products WHERE category = ? ORDER BY id DESC LIMIT 6");
            $stmt->execute([$cat]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $p): ?>
                <div class="col-6 col-md-4 col-lg-2 product-item" data-name="<?= strtolower($p['name']) ?>">
                    <a href="product_details.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:white;">
                        <div class="product-card">
                          <?php if ($p['is_new'] == 1): ?>
    <span class="badge bg-danger position-absolute top-0 start-0 m-2">NEW</span>
<?php endif; ?>

                            <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="product-img">
                            <h6 class="mt-2"><?= htmlspecialchars($p['name']) ?></h6>
                            <small class="text-warning">RWF <?= number_format($p['price']) ?></small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>

        <a href="view_category.php?cat=<?= urlencode($cat) ?>" class="btn btn-warning btn-sm mt-2">
            See more →
        </a>

    <?php endforeach; ?>

</div>
<?php
include "../includes/index_footer.php";
?>


<script>
// LIVE SEARCH FILTER
document.getElementById("search").addEventListener("keyup", function () {
    let value = this.value.toLowerCase();

    document.querySelectorAll(".product-item").forEach(item => {
        let name = item.getAttribute("data-name");
        item.style.display = name.includes(value) ? "" : "none";
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('show');
            }
        });
    });

    document.querySelectorAll('.product-card').forEach(card => {
        card.classList.add('fade-in');
        observer.observe(card);
    });
});
</script>

</body>
</html>