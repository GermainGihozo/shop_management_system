<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'branch') {
    header("Location: ../login.php");
    exit;
}

$branch_id = $_SESSION['branch_id'];

// Fetch pending
$stmt = $conn->prepare("SELECT * FROM products WHERE branch_id = ? AND status = 'pending'");
$stmt->execute([$branch_id]);
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch rejected
$stmt = $conn->prepare("SELECT * FROM products WHERE branch_id = ? AND status = 'rejected'");
$stmt->execute([$branch_id]);
$rejected = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Product Status</title>
<link rel="stylesheet" href="../admin/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container py-4">
    <h3 class="mb-4">📦 Product Approval Status</h3>

    <!-- Pending Products -->
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">⏳ Pending Products</h5>
        </div>
        <div class="card-body">

            <?php if (empty($pending)): ?>
                <p class="text-muted">No pending products.</p>
            <?php else: ?>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Submitted On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= number_format($p['price']) ?> RWF</td>
                                <td><?= $p['quantity'] ?></td>
                                <td><?= $p['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>


    <!-- Rejected Products -->
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">❌ Rejected Products</h5>
        </div>
        <div class="card-body">

            <?php if (empty($rejected)): ?>
                <p class="text-muted">No rejected products.</p>
            <?php else: ?>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Rejected On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rejected as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= number_format($p['price']) ?> RWF</td>
                                <td><?= $p['quantity'] ?></td>
                                <td><?= $p['updated_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="../admin/js/bootstrap.bundle.min.js"></script>
</body>
</html>
