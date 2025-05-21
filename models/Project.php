<?php
class Project {
    private $conn;
    private $table = "projects";

    private $project_id;
    private $user_id;
    private $title;
    private $description;
    private $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters and setters
    public function getProjectId() {
        return $this->project_id;
    }

    public function setProjectId($id) {
        $this->project_id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($desc) {
        $this->description = $desc;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($datetime) {
        $this->created_at = $datetime;
    }

    // Create project
    public function create() {
        $query = "INSERT INTO " . $this->table . " (user_id, title, description, created_at) VALUES (:user_id, :title, :description, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        return $stmt->execute();
    }

    // Update project
    public function update() {
        $query = "UPDATE " . $this->table . " SET title = :title, description = :description WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':project_id', $this->project_id);
        return $stmt->execute();
    }

    // Delete project
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $this->project_id);
        return $stmt->execute();
    }

    // Get project by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE project_id = :project_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->setProjectId($row['project_id']);
            $this->setUserId($row['user_id']);
            $this->setTitle($row['title']);
            $this->setDescription($row['description']);
            $this->setCreatedAt($row['created_at']);
            return $this;
        }
        return null;
    }

    // Get all projects by user
 public function getProjectsByUser($user_id) {
    $query = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $this->conn->error);
    }

    $stmt->bind_param("i", $user_id); // 'i' = integer
    $stmt->execute();
    $result = $stmt->get_result();

    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $project = new Project($this->conn);
        $project->setProjectId($row['project_id']);
        $project->setUserId($row['user_id']);
        $project->setTitle($row['title']);
        $project->setDescription($row['description']);
        $projects[] = $project;
    }

    $stmt->close();
    return $projects;
}


}
    