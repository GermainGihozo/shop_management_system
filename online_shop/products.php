<?php
require '../includes/db.php';

// Fetch all products with category names
$stmt = $conn->query("
    SELECT op.*, c.category_name 
    FROM online_products op
    LEFT JOIN categories c ON op.category_id = c.id
    ORDER BY op.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      color: #fff;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      margin-top: 30px;
      margin-bottom: 20px;
      gap: 10px;
    }
    .btn-add {
      background-color: #4b0082;
      color: white;
      border: none;
      transition: 0.3s;
    }
    .btn-add:hover {
      background-color: #6a5acd;
      transform: translateY(-2px);
    }
    table img {
      border-radius: 8px;
      width: 70px;
      height: 70px;
      object-fit: cover;
    }
    .badge-new {
      background: #28a745;
      color: white;
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 0.8rem;
    }
    .card {
      background: #1e1e1e;
      border: none;
      border-radius: 12px;
    }
    .table {
      color: #fff;
    }
    .table-dark {
      background-color: #2c2c2c;
    }
    .table-hover tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
    .category-badge {
      background: rgba(255, 193, 7, 0.1);
      color: #ffc107;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
    }
    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        align-items: flex-start;
      }
      h3 {
        font-size: 1.3rem;
      }
      .table-responsive {
        border-radius: 10px;
        overflow-x: auto;
      }
      table {
        font-size: 0.9rem;
      }
      table th, table td {
        white-space: nowrap;
      }
      .btn {
        font-size: 0.8rem;
        padding: 4px 8px;
      }
      img {
        width: 55px;
        height: 55px;
      }
    }
    @media (max-width: 480px) {
      .btn-add {
        width: 100%;
        text-align: center;
      }
      .card-body {
        padding: 0.5rem;
      }
      .alert {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <?php require 'admin_navbar.php'; ?>

  <div class="container">
    <div class="header">
      <h3 class="fw-bold text-primary mb-0">Manage Products</h3>
      <a href="admin_add_product.php" class="btn btn-add">+ Add New Product</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
      <div class="alert alert-success">✅ Product updated successfully!</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-danger">🗑️ Product deleted successfully!</div>
    <?php endif; ?>

    <div class="card shadow">
      <div class="card-body table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Discount</th>
              <th>Added On</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($products) > 0): ?>
              <?php foreach ($products as $index => $product): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td>
                      <img src="uploads/<?= htmlspecialchars($product['image']) ?>" 
                           alt="<?= htmlspecialchars($product['name']) ?>">
                  </td>
                  <td>
                      <?= htmlspecialchars($product['name']) ?>
                      <?php if ($product['is_new'] == 1): ?>
                          <span class="badge bg-danger ms-1">NEW</span>
                      <?php endif; ?>
                  </td>
                  <td>
                      <?php if ($product['category_name']): ?>
                          <span class="category-badge"><?= htmlspecialchars($product['category_name']) ?></span>
                      <?php else: ?>
                          <span class="text-muted">No category</span>
                      <?php endif; ?>
                  </td>
                  <td>RWF <?= number_format($product['price'], 0) ?></td>
                  <td>
                      <?php if ($product['discount'] > 0): ?>
                          <span class="text-success"><?= $product['discount'] ?>%</span>
                      <?php else: ?>
                          <span class="text-muted">—</span>
                      <?php endif; ?>
                  </td>
                  <td><?= date("M d, Y", strtotime($product['created_at'])) ?></td>
                  <td>
                      <?php if ($product['discount'] > 0): ?>
                          <span class="badge bg-warning">On Sale</span>
                      <?php else: ?>
                          <span class="badge bg-secondary">Regular</span>
                      <?php endif; ?>
                  </td>
                  <td>
                    <a href="product_edit.php?id=<?= $product['id'] ?>" 
                       class="btn btn-sm btn-primary">Edit</a>
                    <a href="product_delete.php?id=<?= $product['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="text-center text-muted py-4">
                  No products found. <a href="admin_add_product.php" class="text-warning">Add your first product</a>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
   
  <?php include '../includes/footer.php'; ?>
</body>
</html>