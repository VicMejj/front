<?php
class User {
    private $conn;

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct($db) {
        $this->conn = $db;
    }

    // Méthode pour créer un utilisateur
    public function create($full_name, $email, $password, $role ) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO user_accounts (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);
        return $stmt->execute();
    }
    

    // Méthode pour vérifier si l'email existe déjà
    public function emailExists($email) {
        // Requête SQL pour vérifier si l'email existe dans la base de données
        $query = "SELECT id FROM user_accounts WHERE email = ? LIMIT 1";

        // Préparer la requête
        $stmt = $this->conn->prepare($query);

        // Lier l'email à la requête
        $stmt->bind_param("s", $email);

        // Exécuter la requête
        $stmt->execute();

        // Vérifier si l'email existe
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return true; // Email existe déjà
        }

        return false; // Email n'existe pas
    }

    // Méthode pour se connecter avec l'email et le mot de passe
    public function login($email, $password, $stored_password, $id) {
        // Requête SQL pour obtenir l'utilisateur avec cet email
        $query = "SELECT id, password FROM user_accounts WHERE email = ? LIMIT 1";

        // Préparer la requête
        $stmt = $this->conn->prepare($query);

        // Lier l'email à la requête
        $stmt->bind_param("s", $email);

        // Exécuter la requête
        $stmt->execute();

        // Stocker le résultat
        $stmt->store_result();

        // Vérifier si un utilisateur est trouvé
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $stored_password);
            $stmt->fetch();

            // Vérifier si le mot de passe correspond
            if (password_verify($password, $stored_password)) {
                return $id; // Connexion réussie
            }
        }

        return false; // Échec de la connexion
    }
}
?>
