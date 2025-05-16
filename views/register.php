<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../controllers/UserAccountController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data safely
    $data = [
        'full_name' => htmlspecialchars($_POST['full_name']),
        'email' => htmlspecialchars($_POST['email']),
        'password' => $_POST['password']
    ];

    $controller = new UserAccountController();
    $result = $controller->create($data);

    if ($result['success']) {
        header("Location: login.php");
        //exit();
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
    <title>Register - MyDashboard</title>
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
            <li><a href="tasks.html">Tasks</a></li>
            <li><a href="wellness.html">Wellness</a></li>
            <li><a href="budget.html">Budget</a></li>
            <li><a href="wishlist.html">Wishlist</a></li>
            <li><a href="agenda.html">Agenda</a></li>
            <li><a href="login.php" class="login-btn">Login</a></li>
        </ul>
    </nav>

    <main class="auth-container">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Sign up to start managing your life</p>
        </div>

        <form class="auth-form" action="register.php" method="POST">
    <?php if (isset($error_message)): ?>
        <div class="error-message" style="color: red; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="full_name" required placeholder="Enter your full name">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Create a password">
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
    </div>

    <button type="submit" class="submit-btn">Create Account</button>
</form>


        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </main>

    <script src="../public/js/main.js"></script>
</body>
</html>