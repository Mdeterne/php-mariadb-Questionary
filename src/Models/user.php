<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        if ($this->conn === null) {
            throw new Exception('Erreur de connexion à la base de données.');
        }
    }

    function findbyEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function findById($id)
    {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function createUserIfNotExists($id, $email, $name)
    {
        // Check by ID first (primary key from CAS)
        $existingUserById = $this->findById($id);
        if ($existingUserById) {
            return $existingUserById['id'];
        }

        // Check by email to avoid duplicates if ID is different
        $existingUser = $this->findbyEmail($email);
        if ($existingUser) {
            return $existingUser['id'];
        } else {
            $query = "INSERT INTO users (id,email, full_name) VALUES (:id, :email, :full_name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':full_name', $name);
            $stmt->execute();
            return $id;
        }
    }
}