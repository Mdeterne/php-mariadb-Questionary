<?php

class Database
{
    private $conn;
    private $db_name = "questionary";

    public function getConnection()
    {
        $this->conn = null;

        $possible_creds = [
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root'],       // MAMP default
            ['host' => '127.0.0.1', 'user' => 'mariadb', 'pass' => 'mariadb'], // Docker default
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],           // WAMP/XAMPP default
            ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],       // Localhost fallback
            ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'mariadb'],      // Docker root default
            ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'socket' => '/tmp/mysql.sock'], // Socket default
            ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'socket' => '/tmp/mysql.sock'],     // Socket no pass
            ['host' => 'localhost', 'user' => 'milan', 'pass' => '', 'socket' => '/tmp/mysql.sock'],    // User no pass
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
                // Continue to next credential
                continue;
            }
        }

        // Fallback or error if no connection worked
        die("Erreur de connexion : Impossible de se connecter à la base de données avec les configurations disponibles.");
    }
}
