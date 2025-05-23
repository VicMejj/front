<?php
session_start();
require_once '../config.php';
require_once '../models/Task.php';
require_once '../controllers/TaskController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

$db = (new Database())->getConnection();
$controller = new TaskController($db);

// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task = new Task();
    $task->setUserId($user_id);
    $task->setTitle($_POST['title']);
    $task->setDescription($_POST['description']);
    $task->setDueDate($_POST['due_date']);
    $task->setCompleted(0);
    $controller->create($task);
    header("Location: tasks.php");
    exit;
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task_id'])) {
    $controller->delete((int)$_POST['delete_task_id']);
    header("Location: tasks.php");
    exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_id'])) {
    $task = new Task();
    $task->setTaskId($_POST['update_task_id']);
    $task->setTitle($_POST['title']);
    $task->setDescription($_POST['description']);
    $task->setDueDate($_POST['due_date']);
    $task->setCompleted(isset($_POST['completed']) ? 1 : 0);
    $controller->update($task);
    header("Location: tasks.php");
    exit;
}

$tasks = $user_id ? $controller->getTasksByUser($user_id) : [];
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Tasks</title>
    <link rel="stylesheet" href="../public/css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="../public/css/styles.css">

</head>

<body>
       <nav class="navbar">
        <div class="brand">MyDashboard</div>
        <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
        <ul class="nav-links">
            <li><a href="index.php" >Dashboard</a></li>
            <li><a href="tasks.php" class="active">Tasks</a></li>
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
        <h1>My Tasks</h1>
        <!-- ✅ Add Form -->
        <?php if ($isLoggedIn): ?>
        <form method="POST" class="task-form">
            <h3>Add Task</h3>
            <input type="text" name="title" placeholder="Title" required />
            <input type="text" name="description" placeholder="Description" required />
            <input type="date" name="due_date" required />
            <button type="submit" name="add_task">➕ Add Task</button>
        </form>
        <?php endif; ?>

        <!-- ✅ Task List with Inline Editing -->
        <?php if (!empty($tasks)): ?>
        <table class="task-table">
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
                        <td><input type="text" name="title" value="<?= htmlspecialchars($task->getTitle()) ?>"
                                required /></td>
                        <td><input type="text" name="description"
                                value="<?= htmlspecialchars($task->getDescription()) ?>" required /></td>
                        <td><input type="date" name="due_date" value="<?= $task->getDueDate() ?>" required /></td>
                        <td>
                            <label style="display:flex;align-items:center;gap:4px;">
                                <input type="checkbox" name="completed" <?= $task->getCompleted() ? 'checked' : '' ?> />
                                <?= $task->getCompleted() ? '✅ Done' : '⏳ Pending' ?>
                            </label>
                        </td>
                        <td class="actions">
                            <!-- Update Task -->
                            <input type="hidden" name="update_task_id" value="<?= $task->getTaskId() ?>">
                            <button type="submit" style="color: green;">💾 Save</button>
                    </form>
                    <!-- Delete Task -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_task_id" value="<?= $task->getTaskId() ?>">
                        <button type="submit" style="color: red;" onclick="return confirm('Are you sure?')">🗑️
                            Delete</button>
                    </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align:center; color: #888;">No tasks yet</p>
        <?php endif; ?>
    </main>


    <script src="public/js/main.js"></script>
</body>

</html>