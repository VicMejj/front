<?php
session_start();
require_once '../config.php';
require_once '../controllers/BudgetController.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = (new Database())->getConnection();
$controller = new BudgetController($db);

// Handle actions
$controller->handlePostRequest($user_id);

// Get current month budget
$current_date = date('Y-m-01');
$summary = $controller->getBudgetSummary($user_id, $current_date);
$items = $controller->getBudgetItems($user_id, $current_date);

// Get item to edit if editing
$editItem = null;
if (isset($_GET['edit'])) {
    $editItem = $controller->getBudgetById($_GET['edit'], $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget - My Dashboard</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .budget-container { max-width: 1100px; margin: 20px auto; padding: 20px; }
        .metric-box { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 10px 0; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn-edit { color: #28a745; text-decoration: none; }
        .btn-delete { color: #dc3545; background: none; border: none; cursor: pointer; }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="brand">MyDashboard</div>
    <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
    <ul class="nav-links">
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="tasks.php">Tasks</a></li>
        <li><a href="projects.php">Projects</a></li>
        <li><a href="wellness.php">Wellness</a></li>
        <li><a href="budget.php" class="active">Budget</a></li>
        <li><a href="wishlist.php">Wishlist</a></li>
        <li><a href="agenda.php">Agenda</a></li>
        <?php if ($isLoggedIn): ?>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main class="dashboard-container">
    <h1>Budget Management</h1>
    
    <!-- Summary Cards -->
    <div class="metrics">
        <div class="metric-box">
            <h3>Total Planned</h3>
            <p>$<?= number_format($summary['total_planned'] ?? 0, 2) ?></p>
        </div>
        <div class="metric-box">
            <h3>Total Spent</h3>
            <p>$<?= number_format($summary['total_spent'] ?? 0, 2) ?></p>
        </div>
    </div>

    <!-- Add/Edit Form -->
    <form method="POST" class="project-form">
        <h3><?= isset($_GET['edit']) ? 'Edit' : 'Add' ?> Budget Item</h3>
        <input type="hidden" name="date_budget" value="<?= $current_date ?>">
        
        <?php if (isset($_GET['edit'])): ?>
            <input type="hidden" name="budget_id" value="<?= $_GET['edit'] ?>">
        <?php endif; ?>

        <input type="text" name="category" placeholder="Category" required 
               value="<?= isset($editItem) ? htmlspecialchars($editItem['category']) : '' ?>">
        
        <input type="number" step="0.01" name="planned_amount" 
               placeholder="Planned Amount" required
               value="<?= isset($editItem) ? $editItem['planned_amount'] : '' ?>">

        <input type="number" step="0.01" name="spent_amount" 
               placeholder="Spent Amount"
               value="<?= isset($editItem) ? $editItem['spent_amount'] : '' ?>">

        <button type="submit" name="<?= isset($_GET['edit']) ? 'update_budget' : 'add_budget' ?>">
            <?= isset($_GET['edit']) ? 'Update' : 'Add' ?> Item
        </button>
    </form>

    <!-- Budget Items Table -->
    <table class="task-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Planned</th>
                <th>Spent</th>
                <th>Difference</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($items): ?>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['category']) ?></td>
                    <td>$<?= number_format($item['planned_amount'], 2) ?></td>
                    <td>$<?= number_format($item['spent_amount'], 2) ?></td>
                    <td>$<?= number_format($item['planned_amount'] - $item['spent_amount'], 2) ?></td>
                    <td>
                        <a href="budget.php?edit=<?= $item['budget_id'] ?>" class="btn-edit">✏️</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="budget_id" value="<?= $item['budget_id'] ?>">
                            <button type="submit" name="delete_budget" class="btn-delete" 
                                    onclick="return confirm('Are you sure?')">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No budget items found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<script src="../public/js/main.js"></script>
</body>
</html>