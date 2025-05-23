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

// Handle Project and Task Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Project
    if (isset($_POST['add_project'])) {
        $project = new Project($db);
        $project->setUserId($user_id);
        $project->setProjectName($_POST['project_title']);
        $project->setDescription($_POST['project_description']);
        $projectController->create($project);
        header("Location: projects.php");
        exit;
    }
}
    // delete project
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete Project
    if (isset($_POST['delete_project'])) {
        if (!empty($_POST['delete_project_id'])) {
            $deleted = $projectController->delete((int)$_POST['delete_project_id']);

            if (!$deleted) {
                error_log("❌ Project deletion failed for ID: " . $_POST['delete_project_id']);
            }
        }
        header("Location: projects.php");
        exit;
    }

    // Update Project
    if (isset($_POST['update_project_id'])) {
        $project = new Project($db);
        $project->setProjectId((int)$_POST['update_project_id']);
        $project->setProjectName($_POST['project_title']);  // Correct method name
        $project->setDescription($_POST['project_description']);
        $projectController->update($project);
        header("Location: projects.php");
        exit;
    }



    // Add Task to Project
      if (isset($_POST['add_task_to_project'])) {
        $task = new Task($db);
        $task->setUserId($user_id);
        $task->setProjectId((int)$_POST['project_id']);
        $task->setTitle($_POST['task_title']);
        $task->setDescription($_POST['task_description']);
        $task->setDueDate($_POST['task_due_date']);
        $task->setCompleted(0);
        $taskController->create($task);
        header("Location: projects.php");
        exit;
    }

    // Update Task
     if (isset($_POST['update_task'])) {
        $task = new Task($db);
        $task->setTaskId((int)$_POST['task_id']);
        $task->setTitle($_POST['task_title']);
        $task->setDescription($_POST['task_description']);
        $task->setDueDate($_POST['task_due_date']);
        $task->setCompleted(isset($_POST['task_completed']) ? 1 : 0);
        $taskController->update($task);
        header("Location: projects.php");
        exit;
    }

    // Delete Task
     if (isset($_POST['delete_task'])) {
        $taskController->delete((int)$_POST['task_id']);
        header("Location: projects.php");
        exit;
    }
}

// Fetch projects for the logged-in user
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
        <li><a href="budget.php">Budget</a></li>
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
    <h1>My Projects</h1>

    <?php if ($isLoggedIn): ?>
        <form method="POST" class="project-form">
            <h3>Add Project</h3>
            <input type="text" name="project_title" placeholder="Project Title" required />
            <input type="text" name="project_description" placeholder="Project Description" required />
            <button type="submit" name="add_project">➕ Add Project</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($projects)): ?>
        <?php foreach ($projects as $project): ?>
            <section class="dashboard-card project-card" style="margin-bottom: 2rem;">
                <form method="POST">
                    <input type="hidden" name="update_project_id" value="<?= (int)$project->getProjectId() ?>" />
                    <input type="text" name="project_title" value="<?= htmlspecialchars($project->getProjectName()) ?>" required />
                    <input type="text" name="project_description" value="<?= htmlspecialchars($project->getDescription()) ?>" required />
                    <button type="submit" style="color: green;">💾 Save Project</button>
                    <input type="hidden" name="delete_project_id" value="<?= (int)$project->getProjectId() ?>" />
                    <button type="submit" name="delete_project" style="color: red;" onclick="return confirm('Are you sure?')">🗑️ Delete Project</button>

            
                </form>

                <h4>Tasks in this project</h4>
                <?php $tasks = $taskController->getTasksByProject($project->getProjectId()); ?>                <?php if (!empty($tasks)): ?>
                    <table class="task-table" style="width: 100%;">
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
                                        <td><input type="date" name="task_due_date" value="<?= htmlspecialchars($task->getDueDate()) ?>" required /></td>
                                        <td>
                                            <label style="display:flex; align-items:center; gap:4px;">
                                                <input type="checkbox" name="task_completed" <?= $task->getCompleted() ? 'checked' : '' ?> />
                                                <?= $task->getCompleted() ? '✅ Done' : '⏳ Pending' ?>
                                            </label>
                                        </td>
                                        <td>
                                            <input type="hidden" name="task_id" value="<?= (int)$task->getTaskId() ?>" />
                                            <button type="submit" name="update_task" style="color: green;">💾 Save</button>
                                            <button type="submit" name="delete_task" onclick="return confirm('Are you sure?')" style="color: red;">🗑️ Delete</button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tasks in this project yet.</p>
                <?php endif; ?>

               <form method="POST" action="">
                    <input type="hidden" name="project_id" value="<?= (int)$project->getProjectId() ?>">
                    <input type="text" name="task_title" placeholder="Task Title" required>
                    <textarea name="task_description" placeholder="Task Description"></textarea>
                    <input type="date" name="task_due_date" required>
                    <button type="submit" name="add_task_to_project">Add Task</button>
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
