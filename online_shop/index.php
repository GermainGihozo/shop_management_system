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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
  padding-top: 70px; /* space for fixed navbar */
}

footer a {
  transition: color 0.3s;
}

footer a:hover {
  color: #00ff99; /* highlight color for icons */
}

</style>
</head>
<body class="bg-dark text-light">
    <!-- HEADER -->
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary fixed-top shadow">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="#">
      <img src="../includes/images/logo.jpg" alt="Company Logo" width="40" height="40" class="me-2 rounded-circle">
      Himshop
    </a>

    <!-- Toggle button for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item mx-2">
          <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item mx-2">
          <a href="login.php" class="btn btn-outline-light btn-sm">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

    <div class="container py-4">

    <h1 class="text-center mb-4">Welcome to Himshop Online</h1>

  <!--About Section -->
  <section id="about" class="text-center mb-5">
    <p class="lead">We offer the best online products with great discounts and fast delivery 🚀</p>
  </section>
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
    <!-- FOOTER (index.php) -->
<footer class="bg-dark text-light text-center py-4 mt-5">
  <div class="container">
    <p class="mb-3">📩 We'd love to hear from you! Reach out anytime.</p>

    <div class="d-flex justify-content-center gap-4 fs-5 mb-3">
      <a href="https://wa.me/250784873039" class="text-light" target="_blank"><i class="bi bi-whatsapp"></i></a>
      <a href="mailto:info@himshop.com" class="text-light"><i class="bi bi-envelope"></i></a>
      <a href="https://facebook.com/yourpage" class="text-light" target="_blank"><i class="bi bi-facebook"></i></a>
      <a href="https://x.com/yourprofile" class="text-light" target="_blank"><i class="bi bi-twitter-x"></i></a>
      <a href="https://www.instagram.com/hillrock_worshipteam/" class="text-light" target="_blank"><i class="bi bi-instagram"></i></a>
    </div>

    <p class="mb-0 small">&copy; <?php echo date("Y"); ?> Himshop Trading. All Rights Reserved.</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
