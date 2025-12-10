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

    public function saveSurvey($user_id, $titre, $description, $access_pin, $qr_code_token, $questions = [])
    {
        $settings = json_encode([
            'description' => $description
        ]);
        $status = 'closed';

        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO surveys (user_id, title, description, access_pin, qr_code_token, status, settings, created_at) VALUES (:user_id, :titre, :description, :access_pin, :qr_code_token, :status, :settings, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':access_pin', $access_pin);
            $stmt->bindParam(':qr_code_token', $qr_code_token);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':settings', $settings);
            $stmt->execute();

            $surveyId = $this->conn->lastInsertId();

            if (!empty($questions) && $surveyId) {
                $sqlQ = "INSERT INTO questions (survey_id, type, label, order_index, is_required) VALUES (:survey_id, :type, :label, :order_index, :is_required)";
                $stmtQ = $this->conn->prepare($sqlQ);

                $sqlOpt = "INSERT INTO question_options (question_id, label, order_index) VALUES (:question_id, :label, :order_index)";
                $stmtOpt = $this->conn->prepare($sqlOpt);

                foreach ($questions as $index => $q) {
                    $type = $q['type'];
                    $label = $q['title'] ?? 'Question sans titre';
                    if (trim($label) === '') $label = 'Question sans titre';
                    $isRequired = isset($q['required']) && $q['required'] ? 1 : 0;

                    // Mapping types
                    $dbType = 'text';
                    if ($type === 'Réponse courte') $dbType = 'short_text';
                    elseif ($type === 'Paragraphe') $dbType = 'long_text';
                    elseif ($type === 'Cases à cocher') $dbType = 'multiple_choice';
                    elseif ($type === 'Choix multiples') $dbType = 'single_choice';
                    elseif ($type === 'Jauge') $dbType = 'scale';

                    $stmtQ->execute([
                        ':survey_id' => $surveyId,
                        ':type' => $dbType,
                        ':label' => $label,
                        ':order_index' => $index,
                        ':is_required' => $isRequired
                    ]);

                    $questionId = $this->conn->lastInsertId();

                    // Insert Options
                    if (in_array($type, ['Cases à cocher', 'Choix multiples']) && !empty($q['options'])) {
                        foreach ($q['options'] as $optIndex => $opt) {
                            $optLabel = $opt['label'];
                            $stmtOpt->execute([
                                ':question_id' => $questionId,
                                ':label' => $optLabel,
                                ':order_index' => $optIndex
                            ]);
                        }
                    }
                }
            }

            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e; // Re-throw to be caught by controller
        }
    }

    public function updateSurvey($id, $user_id, $titre, $description, $questions = [])
    {
        $settings = json_encode([
            'description' => $description
        ]);

        try {
            $this->conn->beginTransaction();

            // 1. Update basic info
            $query = "UPDATE surveys SET title = :title, description = :description, settings = :settings WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':settings', $settings);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            // 2. Delete existing questions (options cascade delete usually, or we delete them too)
            // Assuming ON DELETE CASCADE on question_options, otherwise delete options first.
            // Safest: Delete options first then questions
            
            // Get question IDs for this survey to clear options
            $qIdsReq = $this->conn->prepare("SELECT id FROM questions WHERE survey_id = :survey_id");
            $qIdsReq->execute([':survey_id' => $id]);
            $qIds = $qIdsReq->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($qIds)) {
                $placeholders = implode(',', array_fill(0, count($qIds), '?'));
                $delOpt = $this->conn->prepare("DELETE FROM question_options WHERE question_id IN ($placeholders)");
                $delOpt->execute($qIds);
            }

            $delQ = $this->conn->prepare("DELETE FROM questions WHERE survey_id = :survey_id");
            $delQ->execute([':survey_id' => $id]);

            // 3. Re-insert questions (same logic as saveSurvey)
             if (!empty($questions)) {
                $sqlQ = "INSERT INTO questions (survey_id, type, label, order_index, is_required) VALUES (:survey_id, :type, :label, :order_index, :is_required)";
                $stmtQ = $this->conn->prepare($sqlQ);

                $sqlOpt = "INSERT INTO question_options (question_id, label, order_index) VALUES (:question_id, :label, :order_index)";
                $stmtOpt = $this->conn->prepare($sqlOpt);

                foreach ($questions as $index => $q) {
                    $type = $q['type'];
                    $label = $q['title'] ?? 'Question sans titre';
                    if (trim($label) === '') $label = 'Question sans titre';
                    $isRequired = isset($q['required']) && $q['required'] ? 1 : 0;

                    // Mapping types
                    $dbType = 'text';
                    if ($type === 'Réponse courte') $dbType = 'short_text';
                    elseif ($type === 'Paragraphe') $dbType = 'long_text';
                    elseif ($type === 'Cases à cocher') $dbType = 'multiple_choice';
                    elseif ($type === 'Choix multiples') $dbType = 'single_choice';
                    elseif ($type === 'Jauge') $dbType = 'scale';

                    $stmtQ->execute([
                        ':survey_id' => $id,
                        ':type' => $dbType,
                        ':label' => $label,
                        ':order_index' => $index,
                        ':is_required' => $isRequired
                    ]);

                    $questionId = $this->conn->lastInsertId();

                    // Insert Options
                    if (in_array($type, ['Cases à cocher', 'Choix multiples']) && !empty($q['options'])) {
                        foreach ($q['options'] as $optIndex => $opt) {
                            $optLabel = $opt['label'];
                            $stmtOpt->execute([
                                ':question_id' => $questionId,
                                ':label' => $optLabel,
                                ':order_index' => $optIndex
                            ]);
                        }
                    }
                }
            }

            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
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

    /**
     * Supprime un questionnaire appartenant à un utilisateur.
     * @param string $id L'ID du questionnaire.
     * @param int $userId L'ID de l'utilisateur (pour sécurité).
     * @return bool True si supprimé, False sinon.
     */
    public function deleteSurvey($id, $userId)
    {
        if ($this->conn === null) {
            return false;
        }
        $query = "DELETE FROM surveys WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Récupère un questionnaire par son ID.
     * @param string $id
     * @return array|false
     */
    public function getSurveyById($id)
    {
        if ($this->conn === null) return false;
        $req = $this->conn->prepare("SELECT id, title, description, status FROM surveys WHERE id = :id");
        $req->bindParam(':id', $id);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les données d'analyse (questions + options) par ID de questionnaire.
     * @param string $surveyId
     * @return array
     */
    public function getAnalysisData($surveyId)
    {
        // 1. Infos du questionnaire
        $survey = $this->getSurveyById($surveyId);
        if (!$survey) return [];

        // 2. Questions
        $reqQuestions = $this->conn->prepare("
             SELECT id, type, label, order_index, is_required 
             FROM questions 
             WHERE survey_id = :surveyId 
             ORDER BY order_index ASC
         ");
        $reqQuestions->bindParam(':surveyId', $surveyId);
        $reqQuestions->execute();
        $questions = $reqQuestions->fetchAll(PDO::FETCH_ASSOC);

        // 3. Options
        foreach ($questions as &$question) {
            if (in_array($question['type'], ['single_choice', 'multiple_choice'])) {
                $question['options'] = $this->getOptionsByQuestionId($question['id']);
            } else {
                $question['options'] = [];
            }
        }
        
        $survey['questions'] = $questions;
        return $survey;
    }
}