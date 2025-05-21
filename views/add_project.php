<?php
session_start();
require_once '../config.php';
require_once '../controllers/ProjectController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'user_id' => $user_id,
        'project_name' => $_POST['project_name'],
        'description' => $_POST['description'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date']
    ];
    $projectController->store($data);
    header('Location: projects.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Add Project</title></head>
<body>
<h2>Add New Project</h2>
<form method="POST">
    <label>Project Name: <input type="text" name="project_name" required></label><br>
    <label>Description: <textarea name="description"></textarea></label><br>
    <label>Start Date: <input type="date" name="start_date"></label><br>
    <label>End Date: <input type="date" name="end_date"></label><br>
    <button type="submit">Add Project</button>
</form>
<a href="projects.php">Back to Projects</a>
</body>
</html>
