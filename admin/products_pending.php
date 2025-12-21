<?php
session_start();
require '../includes/db.php';
require 'navbar.php';

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch pending products
$stmt = $conn->query("
    SELECT p.*, b.name AS branch_name 
    FROM products p 
    JOIN branches b ON p.branch_id = b.id
    WHERE p.status = 'pending'
    ORDER BY p.created_at DESC
");
$pending_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Product Approvals</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.badge-status {
    font-size: 0.8rem;
}
</style>
</head>

<body class="bg-light">

<div class="container py-4">
    <h3 class="mb-4">⏳ Pending Product Approvals</h3>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

        <?php if (empty($pending_products)): ?>
            <div class="alert alert-info mb-0">
                ✅ No pending products awaiting approval.
            </div>
        <?php else: ?>

        <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($pending_products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= number_format($p['price']) ?> RWF</td>
                    <td><?= $p['quantity'] ?></td>
                    <td><?= htmlspecialchars($p['branch_name']) ?></td>
                    <td>
                        <span class="badge bg-warning badge-status">Pending</span>
                    </td>
                    <td class="d-flex gap-2">
                        <a href="product_approve.php?id=<?= $p['id'] ?>" 
                           class="btn btn-success btn-sm">
                            ✔ Approve
                        </a>

                        <button 
                            class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal<?= $p['id'] ?>">
                            ✖ Reject
                        </button>
                    </td>
                </tr>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal<?= $p['id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="product_reject.php">
                      <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                          <h5 class="modal-title">Reject Product</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $p['id'] ?>">
                          <p>
                            Reject <strong><?= htmlspecialchars($p['name']) ?></strong>?
                          </p>
                          <div class="mb-3">
                            <label class="form-label">Rejection Reason</label>
                            <textarea 
                                name="reason" 
                                class="form-control" 
                                rows="3" 
                                required
                                placeholder="Explain why this product is rejected..."></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-danger">Reject Product</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

            <?php endforeach; ?>

            </tbody>
        </table>
        </div>

        <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
