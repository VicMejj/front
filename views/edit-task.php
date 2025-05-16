<?php
require_once '../config.php';
require_once '../models/Task.php';
require_once '../controllers/TaskController.php';

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../login.php");
    exit;
}

$controller = new TaskController($db);

// ✅ Step 1: Fetch task by ID
$task_id = $_GET['id'] ?? null;
$task = $task_id ? $controller->getTaskById($task_id) : null;

if (!$task || $task->getUserId() !== $user_id) {
    echo "Invalid task or unauthorized access.";
    exit;
}

// ✅ Step 2: Handle update submission
if (isset($_POST['update_task'])) {
    $task->setTitle($_POST['title']);
    $task->setDescription($_POST['description']);
    $task->setDueDate($_POST['due_date']);
    $task->setCompleted(isset($_POST['completed']) ? 1 : 0);

    $controller->update($task);
    header("Location: tasks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Task</title>
  <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
  <div class="dashboard-container">
    <h2>Edit Task</h2>
    <form method="POST">
      <label>Title:</label>
      <input type="text" name="title" value="<?= htmlspecialchars($task->getTitle()) ?>" required><br>

      <label>Description:</label>
      <input type="text" name="description" value="<?= htmlspecialchars($task->getDescription()) ?>" required><br>

      <label>Due Date:</label>
      <input type="date" name="due_date" value="<?= htmlspecialchars($task->getDueDate()) ?>" required><br>

      <label>Status:</label>
      <input type="checkbox" name="completed" <?= $task->getCompleted() ? 'checked' : '' ?>> Done<br><br>

      <button type="submit" name="update_task">Update Task</button>
      <a href="tasks.php">Cancel</a>
    </form>
  </div>
</body>
</html>
