<?php
class Budget {
    private $conn;
    private $table = "budget";

    private $budget_id;
    private $user_id;
    private $category;
    private $planned_amount;
    private $spent_amount;
    private $date_budget;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters
    public function getBudgetId() { return $this->budget_id; }
    public function getUserId() { return $this->user_id; }
    public function getCategory() { return $this->category; }
    public function getPlannedAmount() { return $this->planned_amount; }
    public function getSpentAmount() { return $this->spent_amount; }
    public function getDateBudget() { return $this->date_budget; }

    // Setters
    public function setBudgetId($id) { $this->budget_id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setCategory($category) { $this->category = $category; }
    public function setPlannedAmount($amount) { $this->planned_amount = $amount; }
    public function setSpentAmount($amount) { $this->spent_amount = $amount; }
    public function setDateBudget($date) { $this->date_budget = $date; }

    // CRUD Operations
    public function create() {
        $query = "INSERT INTO $this->table (user_id, category, planned_amount, spent_amount, date_budget) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issds", $this->user_id, $this->category, $this->planned_amount, $this->spent_amount, $this->date_budget);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE $this->table SET category=?, planned_amount=?, spent_amount=?, date_budget=? WHERE budget_id=? AND user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sddssi", $this->category, $this->planned_amount, $this->spent_amount, $this->date_budget, $this->budget_id, $this->user_id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM $this->table WHERE budget_id=? AND user_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $this->budget_id, $this->user_id);
        return $stmt->execute();
    }

    public function getByUserAndDate($user_id, $date) {
        $query = "SELECT * FROM $this->table WHERE user_id=? AND date_budget=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $user_id, $date);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>