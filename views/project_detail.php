<?php
session_start();
require_once '../config.php';
require_once '../controllers/ProjectController.php';
require_once '../controllers/TaskController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$projectController = new ProjectController($db);
$taskController = new TaskController($db);

$projectId = $_GET['id'] ?? null;
if (!$projectId) {
    header("Location: projects.php");
    exit();
}

$project = $projectController->getProjectById($projectId, $user_id);
if (!$project) {
    echo "Project not found or you don't have permission to view it.";
    exit();
}

$tasks = $taskController->getTasksByProject($projectId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Project Details - <?= htmlspecialchars($project->getTitle()) ?></title>
    <link rel="stylesheet" href="../public/css/styles.css" />
</head>

<body>
    <nav class="navbar">
        <div class="brand">MyDashboard</div>
        <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
        <ul class="nav-links">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="projects.php" class="active">Projects</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="wellness.php">Wellness</a></li>
            <li><a href="budget.html">Budget</a></li>
            <li><a href="wishlist.html">Wishlist</a></li>
            <li><a href="agenda.html">Agenda</a></li>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
        </ul>
    </nav>

    <main class="dashboard-container">
        <h1><?= htmlspecialchars($project->getTitle()) ?></h1>
        <p><?= nl2br(htmlspecialchars($project->getDescription())) ?></p>

        <section>
            <h2>Tasks in this Project</h2>
            <?php if ($tasks): ?>
                <ul>
                    <?php foreach ($tasks as $task): ?>
                        <li>
                            <strong><?= htmlspecialchars($task->getTitle()) ?></strong> - 
                            <?= $task->getCompleted() ? '✅ Done' : '⏳ Pending' ?><br>
                            <small><?= htmlspecialchars($task->getDescription()) ?></small><br>
                            <small>Due: <?= htmlspecialchars($task->getDueDate()) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No tasks found in this project.</p>
            <?php endif; ?>
            <a href="tasks.php?project_id=<?= $projectId ?>">Manage Tasks</a>
        </section>
    </main>

    <script src="../public/js/main.js"></script>
</body>

</html>
