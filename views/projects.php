<?php
session_start();

require_once '../config.php';
require_once '../models/Project.php';
require_once '../controllers/projectsController.php';
require_once '../models/Task.php';
require_once '../controllers/TaskController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

$db = (new Database())->getConnection();
$projectController = new ProjectController($db);
$taskController = new TaskController($db);

// Handle Project ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $project = new Project();
    $project->setUserId($user_id);
    $project->setTitle($_POST['project_title']);
    $project->setDescription($_POST['project_description']);
    $projectController->create($project);
    header("Location: projects.php");
    exit;
}

// Handle Project DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project_id'])) {
    $projectController->delete((int)$_POST['delete_project_id']);
    header("Location: projects.php");
    exit;
}

// Handle Project UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project_id'])) {
    $project = new Project();
    $project->setProjectId($_POST['update_project_id']);
    $project->setTitle($_POST['project_title']);
    $project->setDescription($_POST['project_description']);
    $projectController->update($project);
    header("Location: projects.php");
    exit;
}

// Handle Task ADD inside a project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task_to_project'])) {
    $task = new Task();
    $task->setUserId($user_id);
    $task->setProjectId($_POST['project_id']);
    $task->setTitle($_POST['task_title']);
    $task->setDescription($_POST['task_description']);
    $task->setDueDate($_POST['task_due_date']);
    $task->setCompleted(0);
    $taskController->create($task);
    header("Location: projects.php");
    exit;
}

// Handle Task DELETE inside project view
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task_id'])) {
    $taskController->delete((int)$_POST['delete_task_id']);
    header("Location: projects.php");
    exit;
}

// Handle Task UPDATE inside project view
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_id'])) {
    $task = new Task();
    $task->setTaskId($_POST['update_task_id']);
    $task->setTitle($_POST['task_title']);
    $task->setDescription($_POST['task_description']);
    $task->setDueDate($_POST['task_due_date']);
    $task->setCompleted(isset($_POST['task_completed']) ? 1 : 0);
    $taskController->update($task);
    header("Location: projects.php");
    exit;
}

$projects = $user_id ? $projectController->getProjectsByUser($user_id) : [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Projects</title>
    <link rel="stylesheet" href="../public/css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
    <nav class="navbar">
        <div class="brand">MyDashboard</div>
        <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
        <ul class="nav-links">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="projects.php" class="active">Projects</a></li>
            <li><a href="wellness.php">Wellness</a></li>
            <li><a href="budget.html">Budget</a></li>
            <li><a href="wishlist.html">Wishlist</a></li>
            <li><a href="agenda.html">Agenda</a></li>

            <?php if ($isLoggedIn): ?>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
            <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="dashboard-container">
        <h1>My Projects</h1>

        <?php if ($isLoggedIn): ?>
        <!-- Add Project Form -->
        <form method="POST" class="project-form">
            <h3>Add Project</h3>
            <input type="text" name="project_title" placeholder="Project Title" required />
            <input type="text" name="project_description" placeholder="Project Description" required />
            <button type="submit" name="add_project">➕ Add Project</button>
        </form>
        <?php endif; ?>

        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
            <section class="dashboard-card project-card" style="margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem; border-radius: 8px;">
                <form method="POST" style="margin-bottom: 1rem;">
                    <input type="hidden" name="update_project_id" value="<?= $project->getProjectId() ?>" />
                    <input type="text" name="project_title" value="<?= htmlspecialchars($project->getTitle()) ?>" required />
                    <input type="text" name="project_description" value="<?= htmlspecialchars($project->getDescription()) ?>" required />
                    <button type="submit" style="color: green;">💾 Save Project</button>
                    <button type="submit" name="delete_project_id" value="<?= $project->getProjectId() ?>" style="color: red;" onclick="return confirm('Are you sure to delete this project?')">🗑️ Delete Project</button>
                </form>

                <!-- Tasks inside this project -->
                <h4>Tasks in this project</h4>
                <?php
                    $tasks = $taskController->getTasksByProject($project->getProjectId());
                ?>
                <?php if (!empty($tasks)): ?>
                    <table class="task-table" style="width: 100%; margin-bottom: 1rem;">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                            <tr>
                                <form method="POST">
                                    <td><input type="text" name="task_title" value="<?= htmlspecialchars($task->getTitle()) ?>" required /></td>
                                    <td><input type="text" name="task_description" value="<?= htmlspecialchars($task->getDescription()) ?>" required /></td>
                                    <td><input type="date" name="task_due_date" value="<?= $task->getDueDate() ?>" required /></td>
                                    <td>
                                        <label style="display:flex; align-items:center; gap:4px;">
                                            <input type="checkbox" name="task_completed" <?= $task->getCompleted() ? 'checked' : '' ?> />
                                            <?= $task->getCompleted() ? '✅ Done' : '⏳ Pending' ?>
                                        </label>
                                    </td>
                                    <td class="actions">
                                        <input type="hidden" name="update_task_id" value="<?= $task->getTaskId() ?>" />
                                        <button type="submit" style="color: green;">💾 Save Task</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_task_id" value="<?= $task->getTaskId() ?>" />
                                    <button type="submit" style="color: red;" onclick="return confirm('Are you sure?')">🗑️ Delete</button>
                                </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tasks in this project yet.</p>
                <?php endif; ?>

                <!-- Add Task to this Project -->
                <form method="POST" class="task-form">
                    <h5>Add Task to this Project</h5>
                    <input type="hidden" name="project_id" value="<?= $project->getProjectId() ?>" />
                    <input type="text" name="task_title" placeholder="Task Title" required />
                    <input type="text" name="task_description" placeholder="Task Description" required />
                    <input type="date" name="task_due_date" required />
                    <button type="submit" name="add_task_to_project">➕ Add Task</button>
                </form>
            </section>
            <?php endforeach; ?>
        <?php else: ?>
        <p style="text-align:center; color: #888;">No projects yet</p>
        <?php endif; ?>
    </main>

<script src="../public/js/main.js"></script>
</body>

</html>
