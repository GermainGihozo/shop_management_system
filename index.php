<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Shop Management System</title>
    <link rel="stylesheet" href="branch/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .card {
            border-radius: 15px;
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>

<!-- <?php include 'includes/navbar.php'; ?> -->

<div class="main-content">
    <div class="card shadow p-4 text-center">
        <h1 class="mb-3 fs-3">Welcome to Shop Management System</h1>
        <p class="lead mb-4">Manage sales, branches, stock, and reports efficiently.</p>
        <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
            <a href="login.php" class="btn btn-primary w-100">Login as Admin</a>
            <a href="login.php" class="btn btn-success w-100">Login as Branch User</a>
            <a href="kiny_version" class="btn btn-primary w-100">Change language</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
