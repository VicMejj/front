<?php
require_once '../controllers/BudgetController.php';

// Database connection (replace with your credentials)
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$budgetController = new BudgetController($conn);

// Handle form submissions (if any)
$budgetController->handlePostRequest();

// Fetch budget data for May 2025
$date = '2025-05-01';
$budgets = $budgetController->getBudgetsByDate($date);
$budget = !empty($budgets) ? $budgets[0] : null;

// Fetch category data for bar chart
$categories = $budgetController->getCategoriesByDate($date);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget - My Dashboard</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background-color: #f0f2f5; }
        .budget-container { padding: 20px; max-width: 1200px; margin: 0 auto; text-align: center; }
        .nav-bar { background-color: #1a2526; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-bar a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; }
        .nav-bar a:hover { color: #00aaff; }
        .logout-btn { background-color: #00aaff; padding: 8px 15px; border-radius: 5px; color: white; text-decoration: none; }
        .metrics { display: flex; justify-content: space-around; margin: 20px 0; }
        .metric-box { background-color: #fff; padding: 20px; border-radius: 10px; width: 20%; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .metric-box h3 { margin: 0; font-size: 1.2em; color: #333; }
        .metric-box p { font-size: 1.5em; margin: 10px 0 0; color: #00aaff; }
        .metric-box small { display: block; color: #777; font-size: 0.9em; }
        .charts { display: flex; justify-content: space-around; margin-top: 30px; }
        .chart-placeholder { background-color: #fff; padding: 20px; border-radius: 10px; width: 45%; height: 200px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; color: #777; }
        h1 { font-size: 2em; margin-bottom: 10px; }
        p { color: #555; margin-bottom: 20px; }
    </style>
</head>
<body>
       <nav class="navbar">
        <div class="brand">MyDashboard</div>
        <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="wellness.php">Wellness</a></li>
            <li><a href="budget.php">Budget</a></li>
            <li><a href="wishlist.php">Wishlist</a></li>
            <li><a href="agenda.php">Agenda</a></li>
            <li><a href="projects.php">Projects</a></li>


            <?php if ($isLoggedIn): ?>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
            <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="budget-container">
        <h1>Budget Overview</h1>
        <p>Track your expenses and savings goals</p>

        <?php if ($budget): ?>
            <div class="metrics">
                <div class="metric-box">
                    <h3>Monthly Budget</h3>
                    <p>$<?php echo number_format($budget->getMonthlyBudget(), 2); ?></p>
                    <small>Planned for this month</small>
                </div>
                <div class="metric-box">
                    <h3>Spent This Month</h3>
                    <p>$<?php echo number_format($budget->getSpentThisMonth(), 2); ?></p>
                    <small>Up to date</small>
                </div>
                <div class="metric-box">
                    <h3>Savings Goal</h3>
                    <p>$<?php echo number_format($budget->getSavingsGoal(), 2); ?></p>
                    <small>For this month</small>
                </div>
                <div class="metric-box">
                    <h3>Remaining Budget</h3>
                    <p>$<?php echo number_format($budget->getRemainingBudget(), 2); ?></p>
                    <small>Left to spend</small>
                </div>
            </div>

            <div class="charts">
                <div class="chart-placeholder">[Bar Chart: Monthly Spending by Category]</div>
                <div class="chart-placeholder">[Line Chart: Savings Progress Over Time]</div>
            </div>
        <?php else: ?>
            <p>No budget data available for this month.</p>
        <?php endif; ?>
    </div>

    <?php $conn->close(); ?>
</body>
</html>