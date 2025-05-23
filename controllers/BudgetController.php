<?php
require_once __DIR__ . '/../models/Budget.php';

class BudgetController {
    private $db;
    private $table = 'budget'; // Add table name here

    public function __construct($db) {
        $this->db = $db;
    }

    public function handlePostRequest($user_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_budget'])) {
                $budget = new Budget($this->db);
                $budget->setUserId($user_id);
                $budget->setCategory($_POST['category']);
                $budget->setPlannedAmount($_POST['planned_amount']);
                $budget->setSpentAmount($_POST['spent_amount'] ?? 0);
                $budget->setDateBudget($_POST['date_budget']);
                $budget->create();
            }

            if (isset($_POST['update_budget'])) {
                $budget = new Budget($this->db);
                $budget->setBudgetId($_POST['budget_id']);
                $budget->setUserId($user_id);
                $budget->setCategory($_POST['category']);
                $budget->setPlannedAmount($_POST['planned_amount']);
                $budget->setSpentAmount($_POST['spent_amount']);
                $budget->setDateBudget($_POST['date_budget']);
                $budget->update();
            }

            if (isset($_POST['delete_budget'])) {
                $budget = new Budget($this->db);
                $budget->setBudgetId($_POST['budget_id']);
                $budget->setUserId($user_id);
                $budget->delete();
            }

            header("Location: budget.php");
            exit;
        }
    }

    public function getBudgetSummary($user_id, $date) {
        $query = "SELECT 
                    SUM(planned_amount) as total_planned,
                    SUM(spent_amount) as total_spent 
                  FROM $this->table 
                  WHERE user_id=? AND date_budget=?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $user_id, $date);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getBudgetItems($user_id, $date) {
        $query = "SELECT * FROM $this->table 
                  WHERE user_id=? AND date_budget=?
                  ORDER BY category";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $user_id, $date);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getBudgetById($budget_id, $user_id) {
        $query = "SELECT * FROM $this->table 
                  WHERE budget_id=? AND user_id=?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $budget_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>