<?php
require_once __DIR__ . '/src/Models/Database.php';
require_once __DIR__ . '/src/Models/questionnaire.php';
require_once __DIR__ . '/src/Models/reponse.php';

// Setup
$questionnaireModel = new questionnaire();
$reponseModel = new Reponse();

echo "Creating new Fake Survey...\n";

// 1. Create Survey
$userId = 1; // Assuming admin/user ID 1 exists
$title = "Enquête de Satisfaction " . date('Y-m-d H:i');
$description = "Ceci est un questionnaire généré automatiquement pour tester les réponses.";
$accessPin = rand(10000, 99999);
$qrCodeToken = bin2hex(random_bytes(16));

$questions = [
    [
        'type' => 'Réponse courte', // short_text
        'title' => 'Quel est votre prénom ?',
        'required' => true,
        'options' => []
    ],
    [
        'type' => 'Paragraphe', // long_text
        'title' => 'Pourquoi avez-vous choisi notre produit ?',
        'required' => false,
        'options' => []
    ],
    [
        'type' => 'Paragraphe', // long_text
        'title' => 'Vos suggestions d\'amélioration ?',
        'required' => false,
        'options' => []
    ],
    [
        'type' => 'Jauge', // scale
        'title' => 'Notez la qualité du service (1-5)',
        'required' => true,
        'options' => []
    ],
    [
        'type' => 'Choix multiples', // single_choice (Radio) (Based on questionnaire.php mapping)
        'title' => 'Quelle est votre tranche d\'âge ?',
        'required' => true,
        'options' => [
            ['label' => '- de 18 ans'],
            ['label' => '18 - 25 ans'],
            ['label' => '26 - 45 ans'],
            ['label' => '45+ ans']
        ]
    ],
    [
        'type' => 'Cases à cocher', // multiple_choice (Checkbox) (Based on questionnaire.php mapping)
        'title' => 'Comment nous avez-vous connus ? (Plusieurs choix possibles)',
        'required' => false,
        'options' => [
            ['label' => 'Réseaux Sociaux'],
            ['label' => 'Bouche à oreille'],
            ['label' => 'Publicité TV'],
            ['label' => 'Recherche Google']
        ]
    ]
];

try {
    $surveyId = $questionnaireModel->saveSurvey($userId, $title, $description, $accessPin, $qrCodeToken, $questions);
    if (!$surveyId) {
        throw new Exception("Error creating survey, no ID returned.");
    }
    echo "Survey Created! ID: $surveyId (PIN: $accessPin)\n";
} catch (Exception $e) {
    die("Error creating survey: " . $e->getMessage() . "\n");
}

// 2. Generate Responses
$count = 30;
echo "Adding $count responses...\n";

// Fetch fresh question structure from DB to get IDs
$surveyData = $questionnaireModel->getAnalysisData($surveyId);
if (!$surveyData)
    die("Could not fetch created survey data.\n");
$dbQuestions = $surveyData['questions'];

$fakerNames = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Heidi', 'Ivan', 'Judy'];
// Rich sentiments for testing analysis
$richComments = [
    // Positive
    "C'est vraiment super, j'adore le design et la rapidité.",
    "Excellent produit, je le recommande vivement à tout le monde. Très facile à utiliser.",
    "Une expérience fantastique, le support est top et très réactif.",
    "Très satisfait, bravo pour le travail accompli. C'est parfait.",
    "Génial, rien à redire. Tout fonctionne comme prévu.",

    // Neutral / Suggestions
    "C'est pas mal mais il manque quelques fonctionnalités essentielles.",
    "Correct dans l'ensemble, mais l'interface pourrait être plus intuitive.",
    "Bien, mais un peu complexe pour les débutants. À améliorer.",
    "Service convenable, sans plus. J'attends les mises à jour.",
    "Pourquoi pas, mais je préfère l'ancienne version pour l'instant.",

    // Negative
    "Vraiment déçu, c'est trop lent et ça bug souvent.",
    "Je n'aime pas du tout la nouvelle mise à jour, c'est une catastrophe.",
    "Le service client est horrible, aucune réponse à mes mails.",
    "Trop cher pour ce que c'est. Je ne renouvellerai pas.",
    "Mauvaise expérience, je suis parti chez la concurrence."
];

for ($i = 0; $i < $count; $i++) {
    $answers = [];

    foreach ($dbQuestions as $q) {
        $qId = $q['id'];
        $type = $q['type']; // 'short_text', 'long_text', 'scale', 'single_choice', 'multiple_choice'

        $value = null;

        switch ($type) {
            case 'short_text':
                $value = $fakerNames[array_rand($fakerNames)];
                break;
            case 'long_text':
                // Use rich comments for the paragraph question
                $value = $richComments[array_rand($richComments)];
                break;
            case 'scale':
                $value = rand(1, 5);
                break;
            case 'single_choice': // Radio
                if (!empty($q['options'])) {
                    $opt = $q['options'][array_rand($q['options'])];
                    $value = $opt['id']; // Reponse model expects Option ID for single choice?
                    // Let's check Reponse::saveFullResponse
                    // ... if (is_numeric($value)) $optionIds[] = $value ...
                    // Yes, it expects option IDs.
                }
                break;
            case 'multiple_choice': // Checkbox
                if (!empty($q['options'])) {
                    $opts = $q['options'];
                    shuffle($opts);
                    $nb = rand(0, count($opts));
                    $selected = array_slice($opts, 0, $nb);
                    $value = array_map(function ($o) {
                        return $o['id'];
                    }, $selected);
                }
                break;
        }

        if ($value !== null && $value !== "" && $value !== []) {
            $answers[$qId] = $value;
        }
    }

    $reponseModel->saveFullResponse($surveyId, $answers);
}

echo "Done. Added $count responses to Survey #$surveyId.\n";
