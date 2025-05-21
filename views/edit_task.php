<?php
session_start();
require_once '../config.php';
require_once '../controllers/TaskController.php';

$database = new Database();
$db = $database->getConnection();

$taskController = new TaskController($db);
$user_id = $_SESSION['user_id'];

$task_id = $_GET['id'] ?? null;
$project_id = $_GET['project_id'] ?? null;

if (!$task_id || !$project_id) {
    header("Location: project_detail.php?id=$project_id");
    exit;
}

$task = $taskController->getById($task_id);

if (!$task || $task['user_id'] != $user_id) {
    die('Task not found or access denied');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'due_date' => $_POST['due_date'],
        'completed' => isset($_POST['completed']) ? 1 : 0
    ];
    $taskController->update($task_id, $data);
    header("Location: project_detail.php?id=$project_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Edit Task</title></head>
<body>
<h2>Edit Task</h2>
<form method="POST">
    <label>Title: <input type="text" name="title" required value="<?= htmlspecialchars($task['title']) ?>"></label><br>
    <label>Description: <textarea name="description"><?= htmlspecialchars($task['description']) ?></textarea></label><br>
    <label>Due Date: <input type="date" name="due_date" value="<?= $task['due_date'] ?>"></label><br>
    <label>Completed: <input type="checkbox" name="completed" <?= $task['completed'] ? 'checked' : '' ?>></label><br>
    <button type="submit">Update Task</button>
</form>
<a href="project_detail.php?id=<?= $project_id ?>">Back to Project</a>
</body>
</html>
