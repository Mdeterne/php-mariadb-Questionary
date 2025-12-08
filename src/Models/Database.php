<?php
class Database
{

    private $host = "127.0.0.1";
    //private $db_name = "form";
    //private $username = "root";
    //private $password = "root";
    private $db_name = "mariadb";
    private $username = "root";
    private $password = "mariadb";
    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // echo "erreur de connexion : " . $exception->getMessage();
        }
        return $this->conn;
    }

}
?>