<?php
class Database
{


    // Configuration MAMP STANDARD (Port 3306, root/root)
    private $host = "127.0.0.1";
    private $db_name = "questionary";
    private $username = "root";
    private $password = "root";
    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Port 3306 is standard MySQL port.
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=3306;dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
             echo "Erreur de connexion BDD : " . $exception->getMessage();
        }
        return $this->conn;
    }

}
?>