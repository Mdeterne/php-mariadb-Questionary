<?php
// seed_responses.php

require_once __DIR__ . '/src/Models/Database.php';
require_once __DIR__ . '/src/Models/questionnaire.php';
require_once __DIR__ . '/src/Models/reponse.php';

try {
    echo "--- Début du seed des réponses ---\n";

    // 1. Récupérer le questionnaire "Questionnaire de Démonstration" (PIN: TEST01)
    $questionnaireModel = new questionnaire();
    $survey = $questionnaireModel->exists('TEST01'); // returns survey info if exists

    if (!$survey) {
        throw new Exception("Le questionnaire avec PIN 'TEST01' n'existe pas. Lancez d'abord seed_default.php");
    }

    $surveyId = $survey['id'];
    echo "✅ Questionnaire trouvé: ID $surveyId\n";

    // 2. Récupérer la structure détaillée (questions + options)
    $surveyFull = $questionnaireModel->listerLesQuestions('TEST01');
    $questions = $surveyFull['questions'];

    // 3. Générer des réponses fictives
    $reponseModel = new Reponse();
    $nbReponses = 20; // Nombre de réponses à simuler

    echo "Génération de $nbReponses réponses...\n";

    $firstNames = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Heidi', 'Ivan', 'Judy'];
    $comments = [
        "Super application !", "Un peu lent par moments.", "J'adore l'interface.",
        "Manque quelques fonctionnalités.", "Parfait pour mon usage.", "Très intuitif.",
        "Je recommande.", "Pas mal du tout.", "À améliorer.", "Simple et efficace."
    ];

    for ($i = 0; $i < $nbReponses; $i++) {
        $answers = [];

        foreach ($questions as $q) {
            $qId = $q['id'];
            $type = $q['type'];
            $options = $q['options'];

            // Logique de réponse aléatoire selon le type
            switch ($type) {
                case 'short_text':
                    // Exemple: Prénom
                    $answers[$qId] = $firstNames[array_rand($firstNames)];
                    break;
                
                case 'long_text': // Paragraph
                    // Exemple: Commentaire
                    if (rand(0, 1)) { // 50% chance de laisser un commentaire
                        $answers[$qId] = $comments[array_rand($comments)];
                    } else {
                        $answers[$qId] = "";
                    }
                    break;

                case 'single_choice': // Radio
                    // Choisir une option au hasard
                    if (!empty($options)) {
                        $randomOpt = $options[array_rand($options)];
                        $answers[$qId] = $randomOpt['id'];
                    }
                    break;

                case 'multiple_choice': // Checkbox
                    // Choisir 0 à N options
                    if (!empty($options)) {
                        $nbChoices = rand(0, count($options));
                        if ($nbChoices > 0) {
                            $randomKeys = array_rand($options, $nbChoices);
                            // array_rand returns key or array of keys
                            if (!is_array($randomKeys)) $randomKeys = [$randomKeys];
                            
                            $selectedIds = [];
                            foreach ($randomKeys as $k) {
                                $selectedIds[] = $options[$k]['id'];
                            }
                            $answers[$qId] = $selectedIds;
                        } else {
                             $answers[$qId] = [];
                        }
                    }
                    break;

                 case 'scale': // Jauge / Note
                    // Note entre 1 et 5 par exemple (ou texte valeur)
                    // Le modèle attend un texte pour la valeur scale dans answers
                    $answers[$qId] = (string) rand(1, 5); 
                    break;
            }
        }

        // Sauvegarder la réponse
        // La méthode saveFullResponse gère l'insertion dans responses, answers, answer_choices
        if ($reponseModel->saveFullResponse($surveyId, $answers)) {
            // echo "."; 
        } else {
            echo "E";
        }
    }

    echo "\n✅ $nbReponses réponses insérées avec succès pour le questionnaire ID $surveyId.\n";
    echo "--- Seed des réponses terminé ---\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
