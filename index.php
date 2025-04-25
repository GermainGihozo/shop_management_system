<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome - Shop Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }
        .card {
            border-radius: 15px;
        }
    </style>
</head>
<body>

<!-- <?php include 'includes/navbar.php'; ?> -->

<div class="main-content">
    <div class="card shadow p-4 text-center">
        <h1 class="mb-3">Welcome to Shop Management System</h1>
        <p class="lead mb-4">Manage sales, branches, stock, and reports efficiently.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="login.php" class="btn btn-primary">Login as Admin</a>
            <a href="login.php" class="btn btn-success">Login as Branch User</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
