    <?php
    class Project {
        private $conn;
        private $table = "projects";

        private $project_id;
        private $user_id;
        private $project_name;
        private $description;
        private $start_date;
        private $end_date;

        public function __construct($db) {
            $this->conn = $db;
        }

        // Getters and Setters
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

        public function getProjectName() {
            return $this->project_name;
        }

        public function setProjectName($name) {
            $this->project_name = $name;
        }

        public function getDescription() {
            return $this->description;
        }

        public function setDescription($desc) {
            $this->description = $desc;
        }

        public function getStartDate() {
            return $this->start_date;
        }

        public function setStartDate($date) {
            $this->start_date = $date;
        }

        public function getEndDate() {
            return $this->end_date;
        }

        public function setEndDate($date) {
            $this->end_date = $date;
        }

        // Create a new project
        public function create() {
            $query = "INSERT INTO $this->table (user_id, project_name, description, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("issss", $this->user_id, $this->project_name, $this->description, $this->start_date, $this->end_date);

            if ($stmt->execute()) {
                return true;
            }

            echo "Execute failed: " . $stmt->error;
            return false;
        }

        // Update project
        public function update() {
            $query = "UPDATE $this->table SET project_name = ?, description = ?, start_date = ?, end_date = ? WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("ssssi", $this->project_name, $this->description, $this->start_date, $this->end_date, $this->project_id);
            return $stmt->execute();
        }

        // Delete project
        public function delete() {
            $query = "DELETE FROM $this->table WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                error_log("❌ Prepare failed: " . $this->conn->error);
                return false;
            }

            $stmt->bind_param("i", $this->project_id);

            if (!$stmt->execute()) {
                error_log("❌ Execute failed: " . $stmt->error);
                return false;
            }

            return true;
        }


        // Get project by ID
        public function getById($id) {
            $query = "SELECT * FROM $this->table WHERE project_id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $this->setProjectId($row['project_id']);
                $this->setUserId($row['user_id']);
                $this->setProjectName($row['project_name']);
                $this->setDescription($row['description']);
                $this->setStartDate($row['start_date']);
                $this->setEndDate($row['end_date']);
                return $this;
            }

            return null;
        }

        // Get all projects by user
        public function getProjectsByUser($user_id) {
            $query = "SELECT * FROM $this->table WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                die("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $projects = [];
            while ($row = $result->fetch_assoc()) {
                $project = new Project($this->conn);
                $project->setProjectId($row['project_id']);
                $project->setUserId($row['user_id']);
                $project->setProjectName($row['project_name']);
                $project->setDescription($row['description']);
                $project->setStartDate($row['start_date']);
                $project->setEndDate($row['end_date']);
                $projects[] = $project;
            }

            $stmt->close();
            return $projects;
        }
    }
    ?>
