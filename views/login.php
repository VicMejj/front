<?php
session_start(); // 🟢 doit être tout en haut avant tout header ou echo

require_once '../controllers/UserAccountController.php';

// ✅ Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ✅ Traiter la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $controller = new UserAccountController();
    $result = $controller->login([
        'email' => $email,
        'password' => $password
    ]);

    if ($result['success']) {
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['full_name'] = $result['user']['full_name'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = $result['message'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyDashboard</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }

        .submit-btn {
            background-color: #3498db;
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #2980b9;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-footer a {
            color: #3498db;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">MyDashboard</div>
        <button class="navbar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-links">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="wellness.html">Wellness</a></li>
            <li><a href="budget.html">Budget</a></li>
            <li><a href="wishlist.html">Wishlist</a></li>
            <li><a href="agenda.html">Agenda</a></li>
            <li><a href="register.php" class="login-btn">Register</a></li>
        </ul>
    </nav>

    <main class="auth-container">
        <div class="auth-header">
            <h1>Login</h1>
            <p>Welcome back! Please login to your account</p>
        </div>

        <form class="auth-form" action="login.php" method="POST">
            <?php if (isset($error_message)): ?>
                <div class="error-message" style="color: red; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </main>

    <script src="../public/js/main.js"></script>
</body>
</html>