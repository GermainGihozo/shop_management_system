<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Murakaza neza - Shop Management System</title>
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
        <h1 class="mb-3">Murakaza neza kuri Shop Management System</h1>
        <p class="lead mb-4">Genzura ibyacurujwe,ibicuruzwa,raporo.</p>
        <div class="d-flex justify-content-center gap-3">
            <!-- <a href="login.php" class="btn btn-primary">Login as Admin</a> -->
            <a href="login.php" class="btn btn-success">Injira nk'umukozi</a>
            <a href="../index.php" class="btn btn-primary">Hindura ururimi</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
