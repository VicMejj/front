<?php
require_once '../models/Budget.php';

class BudgetController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE
    public function create(Budget $budget) {
        $date = $this->conn->real_escape_string($budget->getDate());
        $monthly_budget = $budget->getMonthlyBudget();
        $spent_this_month = $budget->getSpentThisMonth();
        $savings_goal = $budget->getSavingsGoal();
        $remaining_budget = $budget->getRemainingBudget();
        $category = $this->conn->real_escape_string($budget->getCategory());
        $amount = $budget->getAmount();

        $sql = "INSERT INTO budget_overview (date, monthly_budget, spent_this_month, savings_goal, remaining_budget, category, amount)
                VALUES ('$date', $monthly_budget, $spent_this_month, $savings_goal, $remaining_budget, " . 
                ($category ? "'$category'" : "NULL") . ", " . ($amount !== null ? $amount : "NULL") . ")";
        
        if (!$this->conn->query($sql)) {
            die("<p style='color:red;'>❌ SQL ERROR: " . $this->conn->error . "</p>");
        }

        return true;
    }

    // GET ALL (for a specific date, e.g., for charts)
    public function getBudgetsByDate($date) {
        $sql = "SELECT * FROM budget_overview WHERE date = ? AND category IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $budgets = [];
        while ($row = $result->fetch_assoc()) {
            $budget = new Budget();
            $budget->setId($row['id']);
            $budget->setDate($row['date']);
            $budget->setMonthlyBudget($row['monthly_budget']);
            $budget->setSpentThisMonth($row['spent_this_month']);
            $budget->setSavingsGoal($row['savings_goal']);
            $budget->setRemainingBudget($row['remaining_budget']);
            $budget->setCategory($row['category']);
            $budget->setAmount($row['amount']);
            $budgets[] = $budget;
        }

        return $budgets;
    }

    // GET CATEGORIES (for bar chart)
    public function getCategoriesByDate($date) {
        $sql = "SELECT * FROM budget_overview WHERE date = ? AND category IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $budget = new Budget();
            $budget->setId($row['id']);
            $budget->setDate($row['date']);
            $budget->setCategory($row['category']);
            $budget->setAmount($row['amount']);
            $categories[] = $budget;
        }

        return $categories;
    }

    // GET ONE
    public function getBudgetById($id) {
        $sql = "SELECT * FROM budget_overview WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $budget = new Budget();
            $budget->setId($row['id']);
            $budget->setDate($row['date']);
            $budget->setMonthlyBudget($row['monthly_budget']);
            $budget->setSpentThisMonth($row['spent_this_month']);
            $budget->setSavingsGoal($row['savings_goal']);
            $budget->setRemainingBudget($row['remaining_budget']);
            $budget->setCategory($row['category']);
            $budget->setAmount($row['amount']);
            return $budget;
        }

        return null;
    }

    // UPDATE
    public function update(Budget $budget) {
        $sql = "UPDATE budget_overview SET 
                    date = ?, 
                    monthly_budget = ?, 
                    spent_this_month = ?, 
                    savings_goal = ?, 
                    remaining_budget = ?, 
                    category = ?, 
                    amount = ? 
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $category = $budget->getCategory();
        $amount = $budget->getAmount();
        $stmt->bind_param(
            "sdddssdi",
            $budget->getDate(),
            $budget->getMonthlyBudget(),
            $budget->getSpentThisMonth(),
            $budget->getSavingsGoal(),
            $budget->getRemainingBudget(),
            $category,
            $amount,
            $budget->getId()
        );
        return $stmt->execute();
    }

    // DELETE
    public function delete($id) {
        $sql = "DELETE FROM budget_overview WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // HELPER: handle add/update/delete in one method
    public function handlePostRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ADD
            if (isset($_POST['add_budget'])) {
                $budget = new Budget();
                $budget->setDate($_POST['date']);
                $budget->setMonthlyBudget((float)$_POST['monthly_budget']);
                $budget->setSpentThisMonth((float)$_POST['spent_this_month']);
                $budget->setSavingsGoal((float)$_POST['savings_goal']);
                $budget->setRemainingBudget((float)$_POST['remaining_budget']);
                $budget->setCategory(trim($_POST['category']));
                $budget->setAmount(isset($_POST['amount']) ? (float)$_POST['amount'] : null);
                return $this->create($budget);
            }

            // UPDATE
            if (isset($_POST['update_budget'])) {
                $budget = new Budget();
                $budget->setId((int)$_POST['id']);
                $budget->setDate($_POST['date']);
                $budget->setMonthlyBudget((float)$_POST['monthly_budget']);
                $budget->setSpentThisMonth((float)$_POST['spent_this_month']);
                $budget->setSavingsGoal((float)$_POST['savings_goal']);
                $budget->setRemainingBudget((float)$_POST['remaining_budget']);
                $budget->setCategory(trim($_POST['category']));
                $budget->setAmount(isset($_POST['amount']) ? (float)$_POST['amount'] : null);
                return $this->update($budget);
            }

            // DELETE
            if (isset($_POST['delete_budget_id'])) {
                return $this->delete((int)$_POST['delete_budget_id']);
            }
        }

        return false;
    }
}