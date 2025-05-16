<?php

class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = ''; // ⚠️ change si tu as défini un mot de passe
    private $dbname = 'dashbord'; // ✅ remplace par le vrai nom

    public function getConnection() {
        $conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) {
            die("❌ Database connection failed: " . $conn->connect_error);
        }

        return $conn;
    }
}
