<?php
session_start();
require_once '../config.php';
require_once '../controllers/TaskController.php';
require_once '../models/Task.php';
require_once '../controllers/WellnessController.php';
require_once '../models/Wellness.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

$tasks = [];
if ($isLoggedIn) {
    $database = new Database();
    $db = $database->getConnection();
    $controller = new TaskController($db);
    $tasks = $controller->getTasksByUser($user_id);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Dashboard</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <main class="dashboard-container">
        <header class="dashboard-header">
            <h1>Welcome to Your Dashboard</h1>
            <p>Manage your tasks, wellness, budget, and more in one place</p>
        </header>

        <div class="dashboard-grid">
            <section class="dashboard-card tasks-preview">
                <h2>Tasks</h2>
                <div class="preview-content">
                    <?php if (!empty($tasks)): ?>
                    <ul style="list-style-type: none; padding: 0;">
                        <?php foreach (array_slice($tasks, 0, 3) as $task): ?>
                        <li
                            style="margin-bottom: 1rem; padding: 0.75rem; background-color: #f9f9f9; border-radius: 8px;">
                            <strong><?= htmlspecialchars($task->getTitle()) ?></strong><br>
                            <small><?= htmlspecialchars($task->getDescription()) ?></small><br>
                            <small>📅 <?= htmlspecialchars($task->getDueDate()) ?></small><br>
                            <span style="font-weight:bold; color:<?= $task->getCompleted() ? 'green' : 'orange' ?>;">
                                <?= $task->getCompleted() ? '✅ Done' : '⏳ Pending' ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="tasks.php" style="text-decoration: none; color: #007BFF;">➡ View All Tasks</a>
                    <?php else: ?>
                    <p>No tasks yet</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="dashboard-card wellness-preview">
                <h2>Wellness Tracker</h2>
                <div class="preview-content">
                    <p>Start tracking your wellness</p>
                </div>
            </section>

            <section class="dashboard-card budget-preview">
                <h2>Budget Overview</h2>
                <div class="preview-content">
                    <p>Set up your budget</p>
                </div>
            </section>

            <section class="dashboard-card wishlist-preview">
                <h2>Wishlist</h2>
                <div class="preview-content">
                    <p>Add items to your wishlist</p>
                </div>
            </section>

            <section class="dashboard-card agenda-preview">
                <h2>Agenda</h2>
                <div class="preview-content">
                    <p>Plan your schedule</p>
                </div>
            </section>
            <section class="dashboard-card projects-preview">
                <h2>Projects</h2>
                <div class="preview-content">
                    <p>Add your projects</p>
                </div>
            </section>
        </div>
    </main>

    <script src="public/js/main.js"></script>
</body>

</html>