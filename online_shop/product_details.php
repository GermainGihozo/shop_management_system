<?php
require '../includes/db.php';
$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM online_products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die("Product not found");

// Business WhatsApp number (without leading 0 for international format)
$whatsappNumber = '250784873039';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #fff;
        }
        .product-img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
            max-height: 400px;
        }
        .card {
            background: #1e1e1e;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        textarea {
            resize: none;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card p-4">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="product-img" alt="">
                        </div>
                        <div class="col-md-7">
                            <h2 class="text-primary"><?= htmlspecialchars($product['name']) ?></h2>
                            <p class="text-light"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            <h4 class="text-light">$<?= $product['price'] ?></h4>
                            <?php if ($product['discount'] > 0): ?>
                                <p class="text-success">Discount: <?= $product['discount'] ?>%</p>
                            <?php endif; ?>

                            <form id="whatsappForm" class="mt-4">
                                <label for="message" class="form-label text-light">Message the seller:</label>
                                <textarea id="message" class="form-control bg-dark text-light" rows="3" placeholder="Type your message here..."></textarea>

                                <button type="button" id="whatsappBtn" class="btn btn-success mt-3">
                                    💬 Chat on WhatsApp
                                </button>
                                <a href="index.php" class="btn btn-outline-light mt-3 ms-2">← Back</a>
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
            + encodeURIComponent(`Hello, I'm interested in "${productName}" priced at $${productPrice}. \n\n${message}`);
        window.open(url, '_blank');
    });
    </script>
</body>
</html>
