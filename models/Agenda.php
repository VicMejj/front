<?php
class Agenda {
    private $conn;
    private $table = "agenda";

    private $id;
    private $user_id;
    private $title;
    private $date;
    private $start_time;
    private $end_time;
    private $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getTitle() { return $this->title; }
    public function setTitle($title) { $this->title = $title; }
    public function getDate() { return $this->date; }
    public function setDate($date) { $this->date = $date; }
    public function getStartTime() { return $this->start_time; }
    public function setStartTime($start_time) { $this->start_time = $start_time; }
    public function getEndTime() { return $this->end_time; }
    public function setEndTime($end_time) { $this->end_time = $end_time; }
    public function getNotes() { return $this->notes; }
    public function setNotes($notes) { $this->notes = $notes; }

    // CRUD Operations
    public function create() {
        $query = "INSERT INTO $this->table (user_id, title, date, start_time, end_time, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssss", $this->user_id, $this->title, $this->date, $this->start_time, $this->end_time, $this->notes);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE $this->table SET title=?, date=?, start_time=?, end_time=?, notes=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssi", $this->title, $this->date, $this->start_time, $this->end_time, $this->notes, $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM $this->table WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }

    public function getByUser($user_id) {
        $query = "SELECT * FROM $this->table WHERE user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>