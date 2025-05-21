<?php
session_start();
require_once '../config.php';
require_once '../controllers/ProjectController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$user_id = $_SESSION['user_id'];

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: projects.php');
    exit;
}

$project = $projectController->show($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'project_name' => $_POST['project_name'],
        'description' => $_POST['description'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date']
    ];
    $projectController->update($id, $data);
    header('Location: projects.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Edit Project</title></head>
<body>
<h2>Edit Project</h2>
<form method="POST">
    <label>Project Name: <input type="text" name="project_name" required value="<?= htmlspecialchars($project['project_name']) ?>"></label><br>
    <label>Description: <textarea name="description"><?= htmlspecialchars($project['description']) ?></textarea></label><br>
    <label>Start Date: <input type="date" name="start_date" value="<?= $project['start_date'] ?>"></label><br>
    <label>End Date: <input type="date" name="end_date" value="<?= $project['end_date'] ?>"></label><br>
    <button type="submit">Update Project</button>
</form>
<a href="projects.php">Back to Projects</a>
</body>
</html>
