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
                $question['word_frequencies'] = $this->calculateWordFrequencies($question['text_answers']);
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

    /**
     * Calcule la fréquence des mots sémantiquement pertinents (NLP / Stemming)
     */
    private function calculateWordFrequencies($answers)
    {
        if (empty($answers)) {
            return [];
        }

        // Mots vides fréquents en français
        $stopWords = ['le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'à', 'en', 'ce', 'pour', 'que', 'qui', 'dans', 'sur', 'par', 'a', 'plus', 'est', 'sont', 'c\'est', 'j\'ai', 'je', 'mon', 'ma', 'mes', 'au', 'aux', 'ne', 'se', 'ce', 'ces', 'son', 'sa', 'ses', 'vos', 'votre', 'nous', 'vous', 'il', 'elle', 'ils', 'elles', 'on', 'mais', 'ou', 'où', 'donc', 'or', 'ni', 'car', 'tout', 'tous', 'toute', 'toutes', 'cela', 'ça', 'comme', 'si', 'y', 'sans', 'sous', 'vers', 'avec', 'rien', 'aucun', 'aucune', 'très', 'trop', 'peu', 'pas', 'assez', 'bien', 'mal', 'faire', 'fait', 'être', 'avoir', 'quand'];

        try {
            $stemmer = \Wamania\Snowball\StemmerFactory::create('fr');
        } catch (\Exception $e) {
            $stemmer = null; // Fallback sécurisé
        }

        $wordCounts = [];
        $stemToOriginals = [];

        foreach ($answers as $text) {
            if (!$text || !is_string($text))
                continue;

            // Nettoyage : minuscules, suppression ponctuation
            $text = mb_strtolower($text, 'UTF-8');
            // Diviser par tout caractère qui n'est pas une lettre, chiffre ou accent
            $words = preg_split('/[\p{P}\s]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($words as $word) {
                // Ignore petits mots et stopwords
                if (mb_strlen($word, 'UTF-8') <= 2 || in_array($word, $stopWords)) {
                    continue;
                }

                $stem = $stemmer ? $stemmer->stem($word) : $word;

                if (!isset($wordCounts[$stem])) {
                    $wordCounts[$stem] = 0;
                    $stemToOriginals[$stem] = [];
                }

                $wordCounts[$stem]++;

                // On garde trace de la forme originale pour l'affichage final
                if (!isset($stemToOriginals[$stem][$word])) {
                    $stemToOriginals[$stem][$word] = 0;
                }
                $stemToOriginals[$stem][$word]++;
            }
        }

        // Préparer le format de sortie pour le frontend
        $results = [];
        foreach ($wordCounts as $stem => $count) {
            // Trouver la forme originale la plus utilisée pour ce stem (ex: entre "voitures" (2) et "voiture" (1), on garde "voitures")
            arsort($stemToOriginals[$stem]);
            $mostFrequentOriginalWord = array_key_first($stemToOriginals[$stem]);

            $results[] = [
                'etiquette' => $mostFrequentOriginalWord,
                'compte' => $count
            ];
        }

        // Trier par les plus fréquents en premier
        usort($results, function ($a, $b) {
            return $b['compte'] <=> $a['compte'];
        });

        // Conserver le top 30
        return array_slice($results, 0, 30);
    }
}