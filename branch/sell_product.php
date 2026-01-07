<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

// Branch-only access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'] ?? null;

if (!$branch_id) {
    die("❌ Branch not linked. Please contact administrator.");
}

// Fetch all products for branch
$stmt = $conn->prepare("
    SELECT id, name, price, quantity, status
    FROM products
    WHERE branch_id = ?
    ORDER BY id DESC
");
$stmt->execute([$branch_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sell Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container py-4">
    <h4 class="mb-3">🛒 Products</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Price (RWF)</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No products found
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2) ?></td>
                    <td><?= $product['quantity'] ?></td>

                    <!-- STATUS BADGE -->
                    <td>
                        <?php if ($product['status'] === 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif ($product['status'] === 'pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Rejected</span>
                        <?php endif; ?>
                    </td>

                    <!-- ACTION -->
                    <td>
                        <?php if ($product['status'] === 'approved' && $product['quantity'] > 0): ?>
                            <a href="sell_product.php?id=<?= $product['id'] ?>"
                               class="btn btn-danger btn-sm">
                                Sell
                            </a>
                        <?php elseif ($product['status'] === 'approved'): ?>
                            <span class="text-muted">Out of stock</span>
                        <?php else: ?>
                            <span class="text-muted">Not allowed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
