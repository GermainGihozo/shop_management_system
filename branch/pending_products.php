<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require '../includes/db.php';
require 'navbar.php';

/* FETCH PENDING PRODUCTS */
$stmt = $conn->query("
    SELECT p.*, b.name AS branch_name
    FROM products p
    JOIN branches b ON p.branch_id = b.id
    WHERE p.status = 'pending'
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-4">
    <h4 class="mb-3">⏳ Pending Products</h4>

    <?php if (!$products): ?>
        <div class="alert alert-info">No pending products.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Branch</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['branch_name']) ?></td>
                    <td><?= number_format($p['price']) ?> RWF</td>
                    <td><?= $p['quantity'] ?></td>
                    <td class="d-flex gap-2">

                        <!-- APPROVE -->
                        <form method="POST" action="product_action.php">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button class="btn btn-success btn-sm">✅ Approve</button>
                        </form>

                        <!-- REJECT -->
                        <button class="btn btn-danger btn-sm"
                            onclick="rejectProduct(<?= $p['id'] ?>)">
                            ❌ Reject
                        </button>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>

<script>
function rejectProduct(id) {
    const reason = prompt("Reason for rejection:");
    if (!reason) return;

    fetch("product_action.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `id=${id}&action=reject&reason=${encodeURIComponent(reason)}`
    }).then(() => location.reload());
}
</script>
