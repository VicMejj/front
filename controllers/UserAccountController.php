<?php
require_once __DIR__ . '/../config.php';

class UserAccountController {
    private $conn;

    public function __construct() {
        $database = new Database(); // instanciation correcte
        $this->conn = $database->getConnection(); // récupère la connexion MySQLi
    }

    public function create($data) {
        $full_name = $data['full_name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $role = $data['role'] ?? 'user';

        $query = "INSERT INTO user_accounts (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'message' => "Prepare failed: " . $this->conn->error];
        }

        $stmt->bind_param("ssss", $full_name, $email, $password, $role);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => "Execute failed: " . $stmt->error];
        }

        return ['success' => true];
    }

    public function getById($id) {
        try {
            $query = "SELECT id, full_name, email, role FROM user_accounts WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching user: ' . $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            $updateFields = [];
            $types = "";
            $values = [];

            if (isset($data['full_name'])) {
                $updateFields[] = "full_name= ?";
                $types .= "s";
                $values[] = $data['full_name'];
            }

            if (isset($data['email'])) {
                $updateFields[] = "email = ?";
                $types .= "s";
                $values[] = $data['email'];
            }

            if (isset($data['password'])) {
                $updateFields[] = "password = ?";
                $types .= "s";
                $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (isset($data['role'])) {
                $updateFields[] = "role = ?";
                $types .= "s";
                $values[] = $data['role'];
            }

            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No fields to update'];
            }

            $query = "UPDATE user_accounts SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $types .= "i";
            $values[] = $id;

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$values);
            $stmt->execute();

            return ['success' => true, 'message' => 'User account updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error updating user account: ' . $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM user_accounts WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            return ['success' => true, 'message' => 'User account deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error deleting user account: ' . $e->getMessage()];
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT id, full_name, email, role FROM user_accounts";
            $result = $this->conn->query($query);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()];
        }
    }

    public function getUsersByRole($role) {
        try {
            $query = "SELECT id, full_name, email, role FROM user_accounts WHERE role = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching users by role: ' . $e->getMessage()];
        }
    }

    public function login($data) {
        try {
            $query = "SELECT id, full_name, password FROM user_accounts WHERE email = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($data['password'], $user['password'])) {
                unset($user['password']);
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user
                ];
            }

            return ['success' => false, 'message' => 'Invalid email or password'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error during login: ' . $e->getMessage()];
        }
    }
}
