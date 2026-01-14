<?php

class Database
{
    private $conn;
    private $db_name = "questionary";

    public function getConnection()
    {
        $this->conn = null;

        $possible_creds = [
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root'],       // Configuration par défaut MAMP
            ['host' => '127.0.0.1', 'user' => 'mariadb', 'pass' => 'mariadb'], // Configuration par défaut Docker
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],           // Configuration par défaut WAMP/XAMPP
            ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],       // Repli Localhost
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'mariadb'],      // Configuration root Docker par défaut
            ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'socket' => '/tmp/mysql.sock'], // Socket par défaut
            ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'socket' => '/tmp/mysql.sock'],     // Socket sans mot de passe
            ['host' => 'localhost', 'user' => 'milan', 'pass' => '', 'socket' => '/tmp/mysql.sock'],    // Utilisateur sans mot de passe
        ];

        foreach ($possible_creds as $cred) {
            try {
                $dsn = "mysql:host=" . $cred['host'] . ";dbname=" . $this->db_name . ";charset=utf8mb4;allowPublicKeyRetrieval=true";
                if (isset($cred['socket'])) {
                    $dsn = "mysql:unix_socket=" . $cred['socket'] . ";dbname=" . $this->db_name . ";charset=utf8mb4;allowPublicKeyRetrieval=true";
                }

                $this->conn = new PDO($dsn, $cred['user'], $cred['pass']);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $this->conn;

            } catch (PDOException $exception) {
                // Passer aux identifiants suivants
                continue;
            }
        }

        // Aucun identifiant n'a fonctionné, erreur fatale
        die("Erreur de connexion : Impossible de se connecter à la base de données avec les configurations disponibles.");
    }
}
