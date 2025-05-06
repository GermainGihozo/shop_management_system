<!-- subscribe.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Subscribe</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Admin Subscription</h2>
    <form action="process_subscription.php" method="POST">
        <div class="mb-3">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Subscribe</button>
    </form>
</div>
</body>
</html>
