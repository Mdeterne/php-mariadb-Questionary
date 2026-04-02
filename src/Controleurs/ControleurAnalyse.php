<?php

class ControleurAnalyse
{
    /**
     * Affiche la page d'analyse des résultats pour un questionnaire donné.
     */
    function index()
    {
        // 1. Récupération de l'ID depuis l'URL
        $idQuestionnaire = $_GET['id'] ?? null;
        if (!$idQuestionnaire) {
            echo "ID du questionnaire manquant.";
            return;
        }

        // 2. Chargement des modèles
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php');
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Reponse.php');

        $modeleQuestionnaire = new Questionnaire();
        $modeleReponse = new Reponse();

        // 3. Vérification de l'existence du questionnaire via son ID
        // Utilisation d'une méthode hypothétique getSurveyById ou existence locale
        $questionnaire = $modeleQuestionnaire->getSurveyById($idQuestionnaire);

        if (!$questionnaire) {
            echo "Questionnaire introuvable.";
            return;
        }

        // 4. Récupération des filtres de date
        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;

        // 5. Récupération du nombre total de réponses filtrées
        $responseCount = $modeleReponse->getTotalResponsesCount($idQuestionnaire, $startDate, $endDate);

        // 6. Récupération des questions et réponses
        $donneesAnalyse = $modeleQuestionnaire->getAnalysisData($idQuestionnaire);

        // Enrichissement des questions avec les statistiques filtrées
        foreach ($donneesAnalyse['questions'] as &$question) {
            $qId = $question['id'];
            if (in_array($question['type'], ['single_choice', 'multiple_choice'])) {
                $question['stats'] = $modeleReponse->getQuestionStats($qId, $startDate, $endDate);
            } elseif ($question['type'] === 'scale' || $question['type'] === 'jauge') {
                $question['stats'] = $modeleReponse->getScaleStats($qId, $startDate, $endDate);
            } elseif (in_array($question['type'], ['text', 'paragraph', 'short_text', 'long_text'])) {
                $question['text_answers'] = $modeleReponse->getTextAnswers($qId, $startDate, $endDate);
            }
        }

        // 6. Préparation des données pour la vue
        // Variables utilisées dans la vue : $pageTitle, $questionsData
        $pageTitle = $questionnaire['title'] ?? 'Analyse';
        $listeQuestions = $donneesAnalyse['questions'] ?? [];
        $questionsData = json_encode($listeQuestions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($questionsData === false) {
            $questionsData = '[]';
        }

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'analyse' . DIRECTORY_SEPARATOR . 'analyse.php');
    }
}