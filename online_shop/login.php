<?php
require '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login form</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
 body{
background-color: darkslateblue;
min-height: 100vh;
display: flex;
justify-content: center;
align-items: center;
}
.container{
    background-color: aliceblue;
    border-radius: 12px;
    align-content: center;
    width: 100%;
    max-width: 400px;
    padding: 30px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

    </style>
</head>
<body>
<div class="container">
    <div class="Login text-center mb-4">User Login</div>
    <div class="alert-message">

    </div>
    <form action="../admin/dashboard.php" method="post">
    <div class="mb-3">
  <label for="Username">Username</label>
  <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password">Password</label>
        <input type="password" name="Password" class="form-control" required>
    </div>
    <div class="mb-3">
        <button class="btn btn-danger w-100">Login</button>
    </div>
    </form>
</div>    

</body>
</html>