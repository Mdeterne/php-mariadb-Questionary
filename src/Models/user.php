<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Database.php';

class User {
    private $bdd;

    public function __construct() {
        $database = new Database();
        $this->bdd = $database->getConnection();
    }
    public function getUserByCredentials($email, $password) {
        $req = $this->bdd->prepare("SELECT id, email, password_hash, full_name FROM users WHERE email = :email");
        $req->bindParam(':email', $email, PDO::PARAM_STR);
        $req->execute();
        $user = $req->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        return null;
    }

    public function createNewUser($email, $password, $fullName) {
        $req = $this->bdd->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $req->bindParam(':email', $email, PDO::PARAM_STR);
        $req->execute();
        if ($req->fetchColumn() > 0) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $req = $this->bdd->prepare("
            INSERT INTO users (email, password_hash, full_name) 
            VALUES (:email, :password_hash, :full_name)
        ");
        $req->bindParam(':email', $email, PDO::PARAM_STR);
        $req->bindParam(':password_hash', $passwordHash, PDO::PARAM_STR);
        $req->bindParam(':full_name', $fullName, PDO::PARAM_STR);

        if ($req->execute()) {
            return $this->bdd->lastInsertId();
        }
        return false;
    }
}