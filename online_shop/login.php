<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/db.php'; 

$error = '';
$username_value = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['Password'];
    $username_value = htmlspecialchars($username);

    if (empty($username) || empty($password)) {
        $error = "❌ Please enter both username and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['branch_id'] = $user['branch_id'] ?? null;

                header("Location: " . ($user['role'] === 'admin' ? '../admin/dashboard.php' : '../branch/dashboard.php'));
                exit;
            } else {
                $error = "❌ Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "A database error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top, #18222d, #0b0a0b);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: "Segoe UI", sans-serif;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            border-radius: 15px;
            background: rgba(20, 20, 20, 0.88);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.07);
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(25px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffc107;
            margin-bottom: 15px;
        }

        .title {
            color: #fff;
            font-weight: 700;
            font-size: 1.7rem;
        }

        label {
            color: #ddd;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-control {
            background: #1c1c1c;
            border: 1px solid #333;
            color: white;
        }

        .form-control:focus {
            background: #1c1c1c;
            border-color: #ffc107;
            box-shadow: 0 0 8px rgba(255,193,7,0.5);
            color: white;
        }

        .btn-login {
            background: #ffc107;
            border: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #e0a800;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(255,193,7,0.4);
        }

        #pwd {
            color: #ffc0f0;
            font-size: 0.85rem;
        }

        .alert {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 28px 20px;
            }
            .title { font-size: 1.4rem; }
        }
    </style>
</head>

<body>

    <div class="login-box text-center">

        <!-- Logo -->
        <img src="../includes/images/logo.jpg" class="logo" alt="Logo">

        <h3 class="title">Welcome Back 👋</h3>
        <p class="text-muted mb-3">Login to continue</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="text-start">

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" value="<?= $username_value ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="Password" id="password" class="form-control" required>

                <div class="mt-2">
                    <input type="checkbox" onclick="password.type = this.checked ? 'text' : 'password'">
                    <label id="pwd">Show password</label>
                </div>
            </div>

            <button class="btn btn-login w-100 py-2">Login</button>

        </form>
    </div>

</body>
</html>
