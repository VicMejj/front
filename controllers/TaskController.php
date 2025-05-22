<?php
require_once '../models/Task.php';

class TaskController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ CREATE
    public function create(Task $task) {
        $user_id = $task->getUserId();
        $project_id = $task->getProjectId(); // ✅ Add this line
        $title = $this->conn->real_escape_string($task->getTitle());
        $desc = $this->conn->real_escape_string($task->getDescription());
        $due = $this->conn->real_escape_string($task->getDueDate());
        $completed = $task->getCompleted();

        // ✅ Include project_id in SQL
        $sql = "INSERT INTO tasks (user_id, project_id, title, description, due_date, completed)
                VALUES ($user_id, $project_id, '$title', '$desc', '$due', $completed)";
        
        if (!$this->conn->query($sql)) {
            die("<p style='color:red;'>❌ SQL ERROR: " . $this->conn->error . "</p>");
        }

        return true;
    }


    // ✅ GET ALL
    public function getTasksByUser($user_id) {
        $sql = "SELECT * FROM tasks WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $task = new Task();
            $task->setTaskId($row['task_id']);
            $task->setUserId($row['user_id']);
            $task->setTitle($row['title']);
            $task->setDescription($row['description']);
            $task->setDueDate($row['due_date']);
            $task->setCompleted($row['completed']);
            $tasks[] = $task;
        }

        return $tasks;
    }

    // ✅ GET TASKS BY PROJECT ID
    public function getTasksByProject($project_id) {
        $sql = "SELECT * FROM tasks WHERE project_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $task = new Task();
            $task->setTaskId($row['task_id']);
            $task->setUserId($row['user_id']);
            $task->setTitle($row['title']);
            $task->setDescription($row['description']);
            $task->setDueDate($row['due_date']);
            $task->setCompleted($row['completed']);
            $task->setProjectId($row['project_id']); // Make sure your Task model supports this
            $tasks[] = $task;
        }

        return $tasks;
    }


    // ✅ GET ONE
    public function getTaskById($task_id) {
        $sql = "SELECT * FROM tasks WHERE task_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $task = new Task();
            $task->setTaskId($row['task_id']);
            $task->setUserId($row['user_id']);
            $task->setTitle($row['title']);
            $task->setDescription($row['description']);
            $task->setDueDate($row['due_date']);
            $task->setCompleted($row['completed']);
            return $task;
        }

        return null;
    }

    // ✅ UPDATE
    public function update(Task $task) {
        $sql = "UPDATE tasks SET 
                    title = ?, 
                    description = ?, 
                    due_date = ?, 
                    completed = ? 
                WHERE task_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssii",
            $task->getTitle(),
            $task->getDescription(),
            $task->getDueDate(),
            $task->getCompleted(),
            $task->getTaskId()
        );
        return $stmt->execute();
    }

    // ✅ DELETE
    public function delete($task_id) {
        $sql = "DELETE FROM tasks WHERE task_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        return $stmt->execute();
    }

    // ✅ HELPER: handle add/update/delete in one method (to call from HTML logic)
    public function handlePostRequest($user_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ✅ ADD
           if (isset($_POST['add_task'])) {
                $task = new Task();
                $task->setUserId($user_id);
                $task->setProjectId($_POST['project_id']); // ✅ this is required
                $task->setTitle(trim($_POST['title']));
                $task->setDescription(trim($_POST['description']));
                $task->setDueDate($_POST['due_date']);
                $task->setCompleted(0);
                return $this->create($task);
            }

            // ✅ UPDATE
            if (isset($_POST['update_task'])) {
                $task = new Task();
                $task->setTaskId($_POST['task_id']);
                $task->setTitle(trim($_POST['title']));
                $task->setDescription(trim($_POST['description']));
                $task->setDueDate($_POST['due_date']);
                $task->setCompleted(isset($_POST['completed']) ? 1 : 0);
                return $this->update($task);
            }

            // ✅ DELETE
            if (isset($_POST['delete_task_id'])) {
                return $this->delete((int)$_POST['delete_task_id']);
            }
        }

        return false;
    }
}