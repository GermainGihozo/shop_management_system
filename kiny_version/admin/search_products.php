<?php
require '../includes/db.php';

$branchId = $_GET['branch'] ?? '';
$search = trim($_GET['search'] ?? '');

$query = "SELECT p.*, b.name AS branch_name 
          FROM products p
          JOIN branches b ON p.branch_id = b.id
          WHERE 1";
$params = [];

if ($branchId !== '') {
    $query .= " AND p.branch_id = ?";
    $params[] = $branchId;
}
if ($search !== '') {
    $query .= " AND p.name LIKE ?";
    $params[] = "%" . $search . "%";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($products):
  foreach ($products as $product): ?>
<tr class="<?= $product['quantity'] < 5 ? 'table-warning' : '' ?>">
  <td><?= htmlspecialchars($product['name']) ?></td>
  <td><?= number_format($product['price']) ?></td>
  <td><?= $product['quantity'] ?></td>
  <td><?= htmlspecialchars($product['branch_name']) ?></td>
  <td><?= $product['quantity'] < 5 ? '⚠️ Low Stock' : '✔️ OK' ?></td>
  <td><a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a></td>
</tr>
<?php endforeach;
else: ?>
<tr><td colspan="6" class="text-center text-muted">No products found.</td></tr>
<?php endif; ?>
