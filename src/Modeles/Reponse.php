<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Database.php';

class Reponse
{
    private $bdd;

    public function __construct()
    {
        $database = new Database();
        $this->bdd = $database->getConnection();
    }
    private function generateUuidV4()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function saveFullResponse($surveyId, $answers, $userId = null)
    {
        if (!$this->bdd) {
            return false;
        }

        $this->bdd->beginTransaction();
        try {
            // $responseId = $this->generateUuidV4(); // Fixed: DB uses INT AUTO_INCREMENT
            $reqResponse = $this->bdd->prepare("INSERT INTO responses (survey_id, user_id, submitted_at) VALUES (:survey_id, :user_id, NOW())");
            if (!$reqResponse->execute([':survey_id' => $surveyId, ':user_id' => $userId])) {
                throw new Exception("Impossible d'insérer la ligne responses");
            }
            $responseId = $this->bdd->lastInsertId();

            $reqAnswer = $this->bdd->prepare("INSERT INTO answers (response_id, question_id, text_value) VALUES (:response_id, :question_id, :text_value)");
            $reqChoices = $this->bdd->prepare("INSERT INTO answer_choices (answer_id, option_id) VALUES (:answer_id, :option_id)");
            $stmtValidateOption = $this->bdd->prepare("SELECT COUNT(*) FROM question_options WHERE id = :option_id AND question_id = :question_id");

            foreach ($answers as $questionId => $value) {
                $questionId = (int) $questionId;
                $textValue = null;
                $optionIds = [];

                // Check question type
                $stmtType = $this->bdd->prepare("SELECT type FROM questions WHERE id = ?");
                $stmtType->execute([$questionId]);
                $qType = $stmtType->fetchColumn();

                if (in_array($qType, ['short_text', 'long_text', 'scale', 'text', 'paragraph', 'jauge'])) {
                    if (is_array($value)) {
                        $textValue = implode(', ', $value); // Fallback
                    } else {
                        $textValue = trim((string) $value);
                    }
                } else {
                    // C'est une question à choix (choix multiple ou unique)
                    if (is_array($value)) {
                        // NOUVEAU : Si la structure est { options: [...], text_value: "..." } pour gérer "Autre"
                        if (isset($value['options']) || isset($value['text_value'])) {
                            $optionIds = isset($value['options']) ? (is_array($value['options']) ? $value['options'] : []) : [];
                            $textValue = isset($value['text_value']) ? trim((string) $value['text_value']) : null;
                        } else {
                            // Cas classique : tableau d'IDs (checkboxes standard)
                            $optionIds = $value;
                        }
                    } elseif (is_numeric($value)) {
                        // Cas classique : ID unique (radio standard)
                        $optionIds[] = (int) $value;
                    } else {
                        // Cas de fallback
                    }
                }

                if (!$reqAnswer->execute([':response_id' => $responseId, ':question_id' => $questionId, ':text_value' => $textValue])) {
                    throw new Exception("Erreur lors de l'enregistrement de l'entrée answers pour QID: $questionId");
                }

                $answerId = $this->bdd->lastInsertId();

                if (!empty($optionIds)) {
                    foreach ($optionIds as $optionId) {
                        $optionId = (int) $optionId;
                        $stmtValidateOption->execute([':option_id' => $optionId, ':question_id' => $questionId]);
                        if ($stmtValidateOption->fetchColumn() == 0) {
                            throw new Exception("Option $optionId invalide pour la question $questionId");
                        }
                        if (!$reqChoices->execute([':answer_id' => $answerId, ':option_id' => $optionId])) {
                            throw new Exception("Erreur lors de l'enregistrement du choix pour OptionID: $optionId");
                        }
                    }
                }
            }

            $this->bdd->commit();
            return true;
        } catch (Exception $e) {
            $this->bdd->rollBack();
            return false;
        }
    }

    public function getTotalResponsesCount($surveyId, $startDate = null, $endDate = null)
    {
        if (!$this->bdd)
            return 0;
        
        $sql = "SELECT COUNT(*) FROM responses WHERE survey_id = :surveyId AND submitted_at IS NOT NULL";
        $params = [':surveyId' => $surveyId];

        if ($startDate) {
            $sql .= " AND DATE(submitted_at) >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(submitted_at) <= :endDate";
            $params[':endDate'] = $endDate;
        }

        $req = $this->bdd->prepare($sql);
        $req->execute($params);
        return $req->fetchColumn();
    }
    public function getQuestionStats($questionId, $startDate = null, $endDate = null)
    {
        if (!$this->bdd)
            return [];

        $sql = "SELECT 
                    qo.label, 
                    COUNT(ac.id) as count
                FROM question_options qo
                LEFT JOIN answer_choices ac ON qo.id = ac.option_id
                LEFT JOIN answers a ON ac.answer_id = a.id
                LEFT JOIN responses r ON a.response_id = r.id
                WHERE qo.question_id = :questionId";
        
        $params = [':questionId' => $questionId];

        if ($startDate) {
            $sql .= " AND DATE(r.submitted_at) >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(r.submitted_at) <= :endDate";
            $params[':endDate'] = $endDate;
        }

        $sql .= " GROUP BY qo.id, qo.label ORDER BY qo.order_index ASC";

        $req = $this->bdd->prepare($sql);
        $req->execute($params);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTextAnswers($questionId, $startDate = null, $endDate = null)
    {
        if (!$this->bdd)
            return [];

        $sql = "SELECT a.text_value 
                FROM answers a
                JOIN responses r ON a.response_id = r.id
                WHERE a.question_id = :questionId AND a.text_value IS NOT NULL AND a.text_value != ''";
        
        $params = [':questionId' => $questionId];

        if ($startDate) {
            $sql .= " AND DATE(r.submitted_at) >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(r.submitted_at) <= :endDate";
            $params[':endDate'] = $endDate;
        }

        $sql .= " ORDER BY a.id DESC";

        $req = $this->bdd->prepare($sql);
        $req->execute($params);
        return $req->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getScaleStats($questionId, $startDate = null, $endDate = null)
    {
        if (!$this->bdd)
            return [];

        $sql = "SELECT a.text_value as label, COUNT(*) as count
                FROM answers a
                JOIN responses r ON a.response_id = r.id
                WHERE a.question_id = :questionId AND a.text_value IS NOT NULL";
        
        $params = [':questionId' => $questionId];

        if ($startDate) {
            $sql .= " AND DATE(r.submitted_at) >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(r.submitted_at) <= :endDate";
            $params[':endDate'] = $endDate;
        }

        $sql .= " GROUP BY a.text_value ORDER BY CAST(a.text_value AS UNSIGNED) ASC";

        $req = $this->bdd->prepare($sql);
        $req->execute($params);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}