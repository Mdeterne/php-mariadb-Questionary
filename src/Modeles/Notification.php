<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Database.php';

class Notification
{
    private $conn;

    function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère toutes les notifications pour un utilisateur, triées par date décroissante.
     */
    public function recupererNotificationsUtilisateur($idUtilisateur)
    {
        $requete = "SELECT id, message, is_read, created_at 
                  FROM notifications 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':user_id', $idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle notification pour un utilisateur.
     */
    public function creerNotification($idUtilisateur, $message)
    {
        $requete = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':user_id', $idUtilisateur);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    /**
     * Marque une notification comme lue.
     */
    public function marquerCommeLu($id, $idUtilisateur)
    {
        $requete = "UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $idUtilisateur);
        return $stmt->execute();
    }
}
