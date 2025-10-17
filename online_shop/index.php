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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      padding-top: 70px;
      background-color: #111;
      color: #eee;
      overflow-x: hidden;
    }

    .navbar-brand img {
      width: 35px;
      height: 35px;
      object-fit: cover;
      border-radius: 50%;
    }

    .card img {
      height: 220px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card img:hover {
      transform: scale(1.05);
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    h1, h2 {
      font-weight: 600;
    }

    footer a {
      transition: color 0.3s ease;
    }

    footer a:hover {
      color: #00ff99;
    }

    /* RESPONSIVE FIXES */
    @media (max-width: 768px) {
      h1 {
        font-size: 1.7rem;
      }
      .navbar-brand {
        font-size: 1.1rem;
      }
      .card img {
        height: 180px;
      }
    }

    @media (max-width: 576px) {
      .card img {
        height: 160px;
      }
      .btn {
        font-size: 0.85rem;
        padding: 6px 10px;
      }
    }
  </style>
</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary fixed-top shadow">
  <div class="container-fluid px-3">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="../includes/images/logo.jpg" alt="Company Logo" class="me-2">
      Himshop
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
          <a href="login.php" class="btn btn-outline-light btn-sm px-3">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container py-5">
  <h1 class="text-center mb-4">Welcome to Himshop Online</h1>

  <!-- ABOUT SECTION -->
  <section id="about" class="text-center mb-5 px-3">
    <p class="lead">We offer the best online products with great discounts and fast delivery 🚀</p>
  </section>

  <!-- NEW ARRIVALS -->
  <h2 class="mb-4 text-center text-warning">🆕 New Arrivals</h2>
  <div class="row g-3 justify-content-center">
    <?php foreach ($newProducts as $p): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card bg-light text-dark h-100">
          <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
          <div class="card-body text-center">
            <h6 class="fw-bold"><?= htmlspecialchars($p['name']) ?></h6>
            <p class="mb-1">Price: <strong>Rwf <?= $p['price'] ?></strong></p>
            <?php if ($p['discount'] > 0): ?>
              <span class="badge bg-success">-<?= $p['discount'] ?>%</span>
            <?php endif; ?>
            <a href="product_details.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning mt-2 w-100">View Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- OTHER PRODUCTS -->
  <h2 class="mt-5 mb-4 text-center text-info">🛍️ Other Products</h2>
  <div class="row g-3 justify-content-center">
    <?php foreach ($otherProducts as $p): ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card bg-light text-dark h-100">
          <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
          <div class="card-body text-center">
            <h6 class="fw-bold"><?= htmlspecialchars($p['name']) ?></h6>
            <p class="mb-1">Price: <strong>Rwf <?= $p['price'] ?></strong></p>
            <a href="product_details.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info mt-2 w-100">View</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- FOOTER -->
<footer class="bg-dark text-light text-center py-4 mt-5">
  <div class="container">
    <p class="mb-3">📩 We'd love to hear from you! Reach out anytime.</p>
    <div class="d-flex justify-content-center gap-4 fs-5 mb-3 flex-wrap">
      <a href="https://wa.me/250784873039" class="text-light" target="_blank"><i class="bi bi-whatsapp"></i></a>
      <a href="mailto:info@himshop.com" class="text-light"><i class="bi bi-envelope"></i></a>
      <a href="https://facebook.com/yourpage" class="text-light" target="_blank"><i class="bi bi-facebook"></i></a>
      <a href="https://x.com/yourprofile" class="text-light" target="_blank"><i class="bi bi-twitter-x"></i></a>
      <a href="https://www.instagram.com/hillrock_worshipteam/" class="text-light" target="_blank"><i class="bi bi-instagram"></i></a>
    </div>
    <p class="mb-0 small">&copy; <?= date("Y"); ?> Himshop Trading. All Rights Reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
