<?php

class Task {
    private $task_id;
    private $user_id;
    private $title;
    private $description;
    private $due_date;
    private $completed;

    public function getTaskId() { return $this->task_id; }
    public function setTaskId($id) { $this->task_id = $id; }

    public function getUserId() { return $this->user_id; }
    public function setUserId($id) { $this->user_id = $id; }

    public function getTitle() { return $this->title; }
    public function setTitle($t) { $this->title = $t; }

    public function getDescription() { return $this->description; }
    public function setDescription($d) { $this->description = $d; }

    public function getDueDate() { return $this->due_date; }
    public function setDueDate($d) { $this->due_date = $d; }

    public function getCompleted() { return $this->completed; }
    public function setCompleted($c) { $this->completed = $c; }
    private $project_id; // Add this property if not already present

    public function getProjectId() {
        return $this->project_id;
    }

    public function setProjectId($project_id) {
        $this->project_id = $project_id;
    }

    
    public function getTasksByProject($project_id) {
    $query = "SELECT * FROM tasks WHERE project_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$project_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}