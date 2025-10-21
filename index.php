<?php
require 'includes/db.php';
$newProducts = $conn->query("SELECT * FROM online_products WHERE is_new = 1 ORDER BY created_at DESC")->fetchAll();
$otherProducts = $conn->query("SELECT * FROM online_products WHERE is_new = 0 ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Online Shop | Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
  body {
    background-color: #111;
    color: #fff;
    padding-top: 70px; /* for fixed navbar space */
  }

  /* Navbar */
  .navbar {
    background-color: #222;
  }
  .navbar-brand img {
    object-fit: cover;
    border-radius: 50%;
  }

  /* Product cards */
  .card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
  }

  /* IMAGE FIX – keeps real proportions */
  .card-img-top {
    width: 100%;
    height: 220px;              /* uniform height */
    object-fit: contain;        /* show entire image */
    background-color: #f5f5f5;  /* neutral bg for transparent/odd ratio images */
  }

  .card-body {
    flex-grow: 1;
  }

  /* Footer */
  footer {
    background-color: #111;
    color: #ccc;
    text-align: center;
    padding: 40px 0;
    margin-top: 50px;
  }
  footer a {
    color: #ccc;
    transition: color 0.3s ease;
  }
  footer a:hover {
    color: #00ff99;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .card-img-top {
      height: 180px;
    }
    .navbar-brand span {
      display: none;
    }
    footer {
      padding: 30px 10px;
    }
  }

  @media (max-width: 576px) {
    .card-img-top {
      height: 160px;
    }
  }
</style>

</head>
<body>
  <!-- HEADER -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">
        <img src="includes/images/logo.jpg" alt="Logo" width="40" height="40" class="me-2">
        <span>Himshop</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item mx-2">
            <a class="nav-link" href="#about">About</a>
          </li>
          <li class="nav-item mx-2">
            <a href="online_shop/login.php" class="btn btn-outline-light btn-sm">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="container py-4">
    <h1 class="mb-4 text-center">Welcome to Himshop Online</h1>

    <section id="about" class="text-center mb-5">
      <p class="lead">We offer the best online products with great discounts and fast delivery 🚀</p>
    </section>

    <!-- NEW PRODUCTS -->
    <h2 class="text-warning mb-4">🆕 New Arrivals</h2>
    <div class="row g-4">
      <?php foreach ($newProducts as $p): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card bg-light text-dark h-100">
          <img src="online_shop/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <div class="card-body text-center">
            <h5><?= htmlspecialchars($p['name']) ?></h5>
            <p>Price: <strong>Rwf<?= $p['price'] ?></strong></p>
            <?php if ($p['discount'] > 0): ?>
              <span class="badge bg-success">-<?= $p['discount'] ?>%</span>
            <?php endif; ?>
            <a href="online_shop/product_details.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning mt-2">View Details</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- OTHER PRODUCTS -->
    <h2 class="text-info mt-5 mb-4">🛍️ Other Products</h2>
    <div class="row g-4">
      <?php foreach ($otherProducts as $p): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card bg-light text-dark h-100">
          <img src="online_shop/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <div class="card-body text-center">
            <h5><?= htmlspecialchars($p['name']) ?></h5>
            <p>Price: <strong>Rwf<?= $p['price'] ?></strong></p>
            <a href="product_details.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info mt-2">View</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <p class="mb-3">📩 We'd love to hear from you! Reach out anytime.</p>
      <div class="d-flex justify-content-center gap-4 fs-4 mb-3">
        <a href="https://wa.me/250784873039" target="_blank"><i class="bi bi-whatsapp"></i></a>
        <a href="mailto:info@himshop.com"><i class="bi bi-envelope"></i></a>
        <a href="https://facebook.com/yourpage" target="_blank"><i class="bi bi-facebook"></i></a>
        <a href="https://x.com/yourprofile" target="_blank"><i class="bi bi-twitter-x"></i></a>
        <a href="https://www.instagram.com/hillrock_worshipteam/" target="_blank"><i class="bi bi-instagram"></i></a>
      </div>
      <p class="small mb-0">&copy; <?= date('Y') ?> Himshop Trading. All Rights Reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
