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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['branch_id'] = $user['branch_id'] ?? null;

                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../branch/dashboard.php");
                }
                exit;
            } else {
                $error = "❌ Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "A database error occurred. Please try again later.";
        }
    }
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
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
            background: linear-gradient(135deg, #0b0a0bff, #2d4d6d);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
        }

        .container {
            background: linear-gradient(120deg, #2b537aff, #0b0a0bff);
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            padding: 35px 30px;
            box-shadow: 0 8px 20px rgba(154, 53, 53, 0.15);
            animation: fadeInUp 1s ease forwards;
            transform: translateY(50px);
            opacity: 0;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .login-title {
            font-size: 1.7rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            color: #e0e0e0;
        }

        #pwd {
            color: #f1bfdaff;
        }

        .form-control:focus {
            border-color: #0d0448ff;
            box-shadow: 0 0 6px rgba(106, 90, 205, 0.5);
        }

        .btn-login {
            background: #f7f7f7;
            color: #4b0082;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #820077ff;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(75, 0, 130, 0.3);
        }

        /* 📱 Responsive adjustments */
        @media (max-width: 576px) {
            body {
                padding: 20px;
                align-items: flex-start;
            }
            .container {
                margin-top: 40px;
                padding: 25px 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            }
            .login-title {
                font-size: 1.4rem;
            }
            .btn-login {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <div class="login-title mb-4">User Login</div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="mb-3 text-start">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($username_value); ?>" required>
            </div>
            <div class="mb-3 text-start">
                <label for="password">Password</label>
                <input type="password" name="Password" id="password" class="form-control" required>
                <div class="mt-2">
                    <input type="checkbox" onclick="password.type = this.checked ? 'text' : 'password'">
                    <label for="show-password" id="pwd">Show password</label>
                </div>
            </div>
            <div class="mb-3">
                <button class="btn btn-login w-100 py-2">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
