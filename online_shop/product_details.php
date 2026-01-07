<?php
require '../includes/db.php';
$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
    SELECT op.*, c.category_name 
    FROM online_products op
    LEFT JOIN categories c ON op.category_id = c.id
    WHERE op.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Calculate discounted price
$discounted_price = $product['price'];
if ($product['discount'] > 0) {
    $discounted_price = $product['price'] * (1 - ($product['discount'] / 100));
}

$whatsappNumber = '250782823729';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #fff;
            overflow-x: hidden;
            padding-top: 56px;
        }
        .card {
            background: #1e1e1e;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .product-img {
            width: 100%;
            height: auto;
            max-height: 450px;
            border-radius: 10px;
            object-fit: contain;
            background-color: #000;
            padding: 10px;
        }
        textarea {
            resize: none;
        }
        .category-badge {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        @media (max-width: 992px) {
            .product-img {
                max-height: 350px;
            }
        }
        @media (max-width: 767px) {
            .product-img {
                max-height: 300px;
                margin-bottom: 1rem;
            }
            h2 {
                font-size: 1.4rem;
            }
            h4 {
                font-size: 1.1rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 0.6rem;
            }
        }
        @media (max-width: 480px) {
            .product-img {
                max-height: 240px;
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <img src="../includes/images/logo.jpg" alt="Logo" width="40" height="40" class="me-2">
                <span class="fw-bold text-warning">HimShop</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <div class="d-flex ms-auto">
                    <a href="login.php" class="btn btn-warning btn-sm px-3">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-11">
                <div class="card p-4">
                    <div class="row g-4 align-items-center">
                        <!-- Product Image -->
                        <div class="col-md-5 col-sm-12 text-center">
                            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" 
                                 class="product-img img-fluid" 
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>

                        <!-- Product Info -->
                        <div class="col-md-7 col-sm-12">
                            <h2 class="text-primary"><?= htmlspecialchars($product['name']) ?></h2>
                            
                            <!-- Category -->
                            <?php if (!empty($product['category_name'])): ?>
                                <p class="mb-2">
                                    <span class="category-badge"><?= htmlspecialchars($product['category_name']) ?></span>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Description -->
                            <p class="text-light"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <?php if ($product['discount'] > 0): ?>
                                    <h4 class="text-light">
                                        <span class="text-muted text-decoration-line-through me-2">
                                            RWF <?= number_format($product['price'], 0) ?>
                                        </span>
                                        <span class="text-warning fw-bold">
                                            RWF <?= number_format($discounted_price, 0) ?>
                                        </span>
                                    </h4>
                                    <p class="text-success mb-0">
                                        <strong>Discount: <?= $product['discount'] ?>% OFF</strong>
                                    </p>
                                <?php else: ?>
                                    <h4 class="text-warning fw-bold">RWF <?= number_format($product['price'], 0) ?></h4>
                                <?php endif; ?>
                            </div>
                            
                            <!-- WhatsApp Form -->
                            <form id="whatsappForm" class="mt-4">
                                <label for="message" class="form-label text-light">Message the seller:</label>
                                <textarea id="message" class="form-control bg-dark text-light" rows="3" 
                                          placeholder="Type your message here (e.g., 'I want to buy this product', 'Is this available?')"></textarea>

                                <div class="d-flex flex-wrap mt-3 gap-2">
                                    <button type="button" id="whatsappBtn" class="btn btn-success flex-grow-1">
                                        💬 Chat on WhatsApp
                                    </button>
                                    <a href="../index.php" class="btn btn-outline-light flex-grow-1">← Back to Home</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "../includes/index_footer.php"; ?>
    
    <script>
    document.getElementById('whatsappBtn').addEventListener('click', function() {
        const message = document.getElementById('message').value.trim();
        const productName = <?= json_encode($product['name']) ?>;
        const productPrice = <?= $product['discount'] > 0 ? $discounted_price : $product['price'] ?>;
        const originalPrice = <?= $product['price'] ?>;
        const discount = <?= $product['discount'] ?>;
        const category = <?= json_encode($product['category_name'] ?? '') ?>;
        
        let whatsappMessage = `Hello, I'm interested in:\n`;
        whatsappMessage += `*${productName}*\n`;
        
        if (category) {
            whatsappMessage += `Category: ${category}\n`;
        }
        
        if (discount > 0) {
            whatsappMessage += `Original Price: RWF ${originalPrice.toLocaleString()}\n`;
            whatsappMessage += `Discount: ${discount}%\n`;
            whatsappMessage += `*Discounted Price: RWF ${productPrice.toLocaleString()}*\n`;
        } else {
            whatsappMessage += `*Price: RWF ${productPrice.toLocaleString()}*\n`;
        }
        
        if (message) {
            whatsappMessage += `\nMy Message: ${message}`;
        } else {
            whatsappMessage += `\nI would like to purchase this product.`;
        }
        
        const url = `https://wa.me/<?= $whatsappNumber ?>?text=${encodeURIComponent(whatsappMessage)}`;
        window.open(url, '_blank');
    });
    </script>
</body>
</html>