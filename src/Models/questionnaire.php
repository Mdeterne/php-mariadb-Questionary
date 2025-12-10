<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Database.php';

class questionnaire
{
    private $conn;

    function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function saveSurvey($user_id, $titre, $description, $access_pin, $qr_code_token)
    {
        $settings = json_encode([
            'description' => $description
        ]);//TODO a changer plus tard
        $status = 'closed';

        $query = "INSERT INTO surveys (user_id, titre, description, access_pin, qr_code_token, status, settings) VALUES (:user_id, :titre, :description, :access_pin, :qr_code_token, :status, :settings)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':access_pin', $access_pin);
        $stmt->bindParam(':qr_code_token', $qr_code_token);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':settings', $settings);
        $stmt->execute();
    }

    /**
     * Vérifie si un code PIN existe et si le questionnaire est actif.
     * @param string $pin Le code PIN à vérifier.
     * @return array|false Les données de base du questionnaire si trouvé, false sinon.
     */
    function exists($pin)
    {
        if ($this->conn === null) {
            return false;
        }
        $req = $this->conn->prepare("
            SELECT id, title, description 
            FROM surveys 
            WHERE access_pin = :pin AND status = 'active'
        ");
        $req->bindParam(':pin', $pin, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les questionnaires d'un utilisateur.
     * @param int $userId
     * @return array
     */
    public function getSurveysByUserId($userId)
    {
        if ($this->conn === null) {
            return [];
        }
        $query = "SELECT id, title as titre, description, access_pin, status, created_at FROM surveys WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Liste toutes les questions et leurs options pour un questionnaire donné par son PIN.
     * @param string $pin Le code PIN du questionnaire.
     * @return array|false Les données complètes du questionnaire (y compris questions et options), ou false si non trouvé.
     */
    function listerLesQuestions($pin)
    {
        // 1. Récupérer les informations de base du questionnaire (ID et statut)
        $survey = $this->exists($pin);

        if (!$survey) {
            return false;
        }

        $surveyId = $survey['id'];

        // 2. Récupérer toutes les questions pour cet ID
        if ($this->conn === null) {
            return false;
        }
        $reqQuestions = $this->conn->prepare("
            SELECT id, type, label, order_index, is_required 
            FROM questions 
            WHERE survey_id = :surveyId 
            ORDER BY order_index ASC
        ");
        $reqQuestions->bindParam(':surveyId', $surveyId, PDO::PARAM_STR);
        $reqQuestions->execute();
        $questions = $reqQuestions->fetchAll(PDO::FETCH_ASSOC);

        // 3. Ajouter les options pour les questions à choix
        foreach ($questions as &$question) {
            if (in_array($question['type'], ['single_choice', 'multiple_choice'])) {
                $question['options'] = $this->getOptionsByQuestionId($question['id']);
            } else {
                $question['options'] = [];
            }
        }
        unset($question);

        $survey['questions'] = $questions;
        return $survey;
    }

    /**
     * Fonction utilitaire pour récupérer les options d'une question.
     * @param int $questionId L'ID de la question.
     * @return array La liste des options.
     */
    private function getOptionsByQuestionId($questionId)
    {
        if ($this->conn === null) {
            return [];
        }
        $req = $this->conn->prepare("
            SELECT id, label, order_index, is_open_ended 
            FROM question_options 
            WHERE question_id = :questionId 
            ORDER BY order_index ASC
        ");
        $req->bindParam(':questionId', $questionId, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Liste les 5 premiers questionnaires actifs ou les plus récents (pour la page d'accueil ou une liste publique).
     * @return array La liste des questionnaires.
     */
    function listerLesQuestionnaires()
    {
        // Cette fonction n'est pas strictement requise par les maquettes, mais peut servir à l'administration.
        if ($this->conn === null) {
            return [];
        }
        $req = $this->conn->prepare("
            SELECT id, title, status, created_at
            FROM surveys
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}