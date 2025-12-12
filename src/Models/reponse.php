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

    public function saveFullResponse($surveyId, $answers)
    {
        if (!$this->bdd) {
            return false;
        }

        $this->bdd->beginTransaction();
        try {
            // $responseId = $this->generateUuidV4(); // Fixed: DB uses INT AUTO_INCREMENT
            $reqResponse = $this->bdd->prepare("INSERT INTO responses (survey_id, submitted_at) VALUES (:survey_id, NOW())");
            if (!$reqResponse->execute([':survey_id' => $surveyId])) {
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
                    // It's a choice question
                    if (is_array($value)) {
                        $optionIds = $value;
                    } elseif (is_numeric($value)) {
                        $optionIds[] = (int) $value;
                    } else {
                        // Fallback for non-numeric single choice?
                        // Maybe it's a string label passed?
                        // For now keep existing behavior for fallbacks or ignore
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
            echo "Error saving response: " . $e->getMessage() . "\n"; // DEBUG
            return false;
        }
    }

    public function getTotalResponsesCount($surveyId)
    {
        if (!$this->bdd)
            return 0;
        $req = $this->bdd->prepare("
            SELECT COUNT(*) FROM responses 
            WHERE survey_id = :surveyId AND submitted_at IS NOT NULL
        ");
        $req->bindParam(':surveyId', $surveyId, PDO::PARAM_STR);
        $req->execute();
        return $req->fetchColumn();
    }
    public function getQuestionStats($questionId)
    {
        if (!$this->bdd)
            return [];
        $req = $this->bdd->prepare("
            SELECT 
                qo.label, 
                COUNT(ac.id) as count
            FROM question_options qo
            LEFT JOIN answer_choices ac ON qo.id = ac.option_id
            WHERE qo.question_id = :questionId
            GROUP BY qo.id, qo.label
            ORDER BY qo.order_index ASC
        ");
        $req->bindParam(':questionId', $questionId, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTextAnswers($questionId)
    {
        if (!$this->bdd)
            return [];
        $req = $this->bdd->prepare("
            SELECT text_value 
            FROM answers 
            WHERE question_id = :questionId AND text_value IS NOT NULL AND text_value != ''
            ORDER BY id DESC
        ");
        $req->bindParam(':questionId', $questionId, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getScaleStats($questionId)
    {
        if (!$this->bdd)
            return [];
        $req = $this->bdd->prepare("
            SELECT text_value as label, COUNT(*) as count
            FROM answers
            WHERE question_id = :questionId AND text_value IS NOT NULL
            GROUP BY text_value
            ORDER BY CAST(text_value AS UNSIGNED) ASC
        ");
        $req->bindParam(':questionId', $questionId, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}