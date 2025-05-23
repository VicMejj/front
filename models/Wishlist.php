<?php
class Wishlist {
    private $conn;
    private $table = "wishlist";

    private $item_id;
    private $user_id;
    private $item_name;
    private $item_url;
    private $priority;
    private $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters and Setters
    public function getItemId() { return $this->item_id; }
    public function setItemId($item_id) { $this->item_id = $item_id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getItemName() { return $this->item_name; }
    public function setItemName($item_name) { $this->item_name = $item_name; }
    public function getItemUrl() { return $this->item_url; }
    public function setItemUrl($item_url) { $this->item_url = $item_url; }
    public function getPriority() { return $this->priority; }
    public function setPriority($priority) { $this->priority = $priority; }
    public function getNotes() { return $this->notes; }
    public function setNotes($notes) { $this->notes = $notes; }

    // CRUD Operations
    public function create() {
        $query = "INSERT INTO $this->table (user_id, item_name, item_url, priority, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issis", $this->user_id, $this->item_name, $this->item_url, $this->priority, $this->notes);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE $this->table SET item_name=?, item_url=?, priority=?, notes=? WHERE item_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssisi", $this->item_name, $this->item_url, $this->priority, $this->notes, $this->item_id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM $this->table WHERE item_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->item_id);
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