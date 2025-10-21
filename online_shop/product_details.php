<?php
require '../includes/db.php';
$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM online_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die("Product not found");

// Business WhatsApp number (without leading 0 for international format)
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
    }

    .card {
        background: #1e1e1e;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
    }

    /* Product image styling */
    .product-img {
        width: 100%;
        height: auto;
        max-height: 450px;
        border-radius: 10px;
        object-fit: contain;         /* shows full image without cropping */
        background-color: #000;      /* keeps background neat for transparent/odd ratio images */
        padding: 10px;               /* adds spacing for large white borders */
    }

    textarea {
        resize: none;
    }

    /* Responsive adjustments */
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
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-11">
                <div class="card p-4">
                    <div class="row g-4 align-items-center">
                        <!-- Product Image -->
                        <div class="col-md-5 col-sm-12 text-center">
                            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="product-img img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>

                        <!-- Product Info -->
                        <div class="col-md-7 col-sm-12">
                            <h2 class="text-primary"><?= htmlspecialchars($product['name']) ?></h2>
                            <p class="text-light"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            <h4 class="text-light fw-bold">RWF <?= number_format($product['price'], 0) ?></h4>

                            <?php if ($product['discount'] > 0): ?>
                                <p class="text-success">Discount: <?= $product['discount'] ?>%</p>
                            <?php endif; ?>

                            <!-- WhatsApp Form -->
                            <form id="whatsappForm" class="mt-4">
                                <label for="message" class="form-label text-light">Message the seller:</label>
                                <textarea id="message" class="form-control bg-dark text-light" rows="3" placeholder="Type your message here..."></textarea>

                                <div class="d-flex flex-wrap mt-3 gap-2">
                                    <button type="button" id="whatsappBtn" class="btn btn-success flex-grow-1">
                                        💬 Chat on WhatsApp
                                    </button>
                                    <a href="index.php" class="btn btn-outline-light flex-grow-1">← Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('whatsappBtn').addEventListener('click', function() {
        const message = document.getElementById('message').value.trim();
        const productName = <?= json_encode($product['name']) ?>;
        const productPrice = <?= json_encode($product['price']) ?>;
        const url = "https://wa.me/<?= $whatsappNumber ?>?text=" 
            + encodeURIComponent(`Hello, I'm interested in "${productName}" priced at RWF ${productPrice}. \n\n${message}`);
        window.open(url, '_blank');
    });
    </script>
</body>
</html>
