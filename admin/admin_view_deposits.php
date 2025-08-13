<?php
session_start();
require '../includes/db.php';
require '../includes/auth.php';
requireRole('admin');

// Fetch branches
$branches = $conn->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Handle filters
$branch_id = $_GET['branch_id'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Build query
$query = "SELECT d.*, b.name AS branch_name FROM deposits d JOIN branches b ON d.branch_id = b.id WHERE 1=1";
$params = [];

if (!empty($branch_id)) {
    $query .= " AND d.branch_id = ?";
    $params[] = $branch_id;
}
if (!empty($from_date)) {
    $query .= " AND d.deposit_date >= ?";
    $params[] = $from_date;
}
if (!empty($to_date)) {
    $query .= " AND d.deposit_date <= ?";
    $params[] = $to_date;
}

$query .= " ORDER BY d.deposit_date DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_amount = array_sum(array_column($deposits, 'amount'));

// Build export query string
$exportParams = http_build_query(array_filter([
    'branch_id' => $branch_id,
    'from_date' => $from_date,
    'to_date' => $to_date
]));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Branch Deposits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="web icon" type="jpg" href="includes/images/logo.jpg">
  
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h4 class="mb-4">🏦 All Branch Deposits</h4>

    <!-- Filter Form -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Branch:</label>
            <select name="branch_id" class="form-select">
                <option value="">All</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $b['id'] == $branch_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">From:</label>
            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">To:</label>
            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">🔍 Filter</button>
        </div>
    </form>

    <!-- Export Buttons -->
    <div class="mb-3">
        <a href="export_deposits_csv.php?<?= $exportParams ?>" class="btn btn-success me-2">⬇️ Export CSV</a>
        <a href="export_deposits_pdf.php?<?= $exportParams ?>" class="btn btn-danger">⬇️ Export PDF</a>
    </div>

    <!-- Data Table -->
    <?php if (empty($deposits)): ?>
        <div class="alert alert-info">No deposits found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Amount (RWF)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deposits as $deposit): ?>
                        <tr>
                            <td><?= htmlspecialchars($deposit['deposit_date']) ?></td>
                            <td><?= htmlspecialchars($deposit['branch_name']) ?></td>
                            <td><?= number_format($deposit['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold table-secondary">
                        <td colspan="2" class="text-end">Total:</td>
                        <td><?= number_format($total_amount, 2) ?> RWF</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
