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
            // Prepared statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['branch_id'] = $user['branch_id'] ?? null; // Use null for safety

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php"); // Assuming login is in root
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
            background: linear-gradient(135deg, #1d1a1f, #2d4d6d);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* --- Form Container Style (Note: Using a darker background now, adjust text color if needed) --- */
        .container {
            /* Changed to a specific color, but you may need to adjust the text colors again */
            background-color: rgba(67, 127, 154, 0.95); 
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            padding: 35px 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 1s ease forwards;
            transform: translateY(50px);
            opacity: 0;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* --- Text and Label Styles (Ensured good contrast on the new blue container) --- */
        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff; /* White text on blue container */
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            color: #e0e0e0; /* Light gray text on blue container */
        }

        /* --- Input Field Focus Style --- */
        .form-control:focus {
            border-color: #6a5acd;
            box-shadow: 0 0 6px rgba(106, 90, 205, 0.5);
        }

        /* --- Login Button Style --- */
        .btn-login {
            background: #f7f7f7; /* Near-White button background */
            color: #4b0082; /* Dark purple text on light button */
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #4b0082; 
            color: white; 
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(75, 0, 130, 0.3);
        }
    </style>
</head>
<body>
    <div class="container floating text-center">
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
            </div>
            <div class="mb-3">
                <button class="btn btn-login w-100 py-2">Login</button>
            </div>
        </form>
    </div>
</body>
</html>