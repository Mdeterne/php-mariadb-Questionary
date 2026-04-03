<?php
require_once 'src/Modeles/Database.php';

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Échec de la connexion à la base de données.");
}

// Récupérer les questionnaires du test
$pins = ['111111', '222222', '333333', '444444'];
$inQuery = implode(',', array_map(function($p) use ($conn) { return $conn->quote($p); }, $pins));
$stmtSurveys = $conn->query("SELECT id, access_pin, title FROM surveys WHERE access_pin IN ($inQuery)");
$surveys = $stmtSurveys->fetchAll(PDO::FETCH_ASSOC);

if (empty($surveys)) {
    die("Aucun questionnaire de test trouvé. Avez-vous lancé le script de seed précédent ?");
}

$dummyTexts = [
    "C'était très intéressant mais un peu trop théorique.",
    "J'ai beaucoup apprécié ce module.",
    "Rien à signaler, tout s'est bien passé.",
    "Des difficultés à suivre au début, mais la fin était claire.",
    "Super expérience ! Je recommande.",
    "Je pense qu'il faudrait plus de travaux pratiques.",
    "C'est OK dans l'ensemble.",
    "Excellent, j'ai appris plein de choses.",
    "Un peu déçu par le contenu, j'attendais autre chose.",
    "RAS."
];

foreach ($surveys as $survey) {
    $surveyId = $survey['id'];
    
    // Récupérer les questions du questionnaire
    $stmtQ = $conn->prepare("SELECT id, type, label FROM questions WHERE survey_id = :sid");
    $stmtQ->execute([':sid' => $surveyId]);
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

    if (empty($questions)) {
        continue; // Pas de questions, on passe
    }

    // Générer entre 3 et 8 réponses au hasard pour chaque questionnaire
    $numResponses = rand(3, 8);
    
    for ($i = 0; $i < $numResponses; $i++) {
        // Créer la soumission
        $stmtResp = $conn->prepare("INSERT INTO responses (survey_id, user_id, started_at, submitted_at) VALUES (:sid, NULL, NOW() - INTERVAL :min MINUTE, NOW())");
        $randomMinutes = rand(2, 45); // Le questionnaire a été rempli il y a X minutes
        $stmtResp->execute([':sid' => $surveyId, ':min' => $randomMinutes]);
        
        $responseId = $conn->lastInsertId();

        // Répondre aux questions
        foreach ($questions as $q) {
            $value = null;
            if ($q['type'] === 'long_text' || $q['type'] === 'short_text') {
                $value = $dummyTexts[array_rand($dummyTexts)];
            } elseif ($q['type'] === 'scale') {
                $value = rand(2, 5); // Une note entre 2 et 5 (par exemple sur 5)
            } else {
                $value = "Option test"; // Fallback pour types choice
            }

            $stmtAns = $conn->prepare("INSERT INTO answers (response_id, question_id, text_value) VALUES (:rid, :qid, :val)");
            $stmtAns->execute([':rid' => $responseId, ':qid' => $q['id'], ':val' => $value]);
        }
    }
    
    echo "Généré $numResponses réponses pour le questionnaire '{$survey['title']}'\n";
}

echo "Génération des réponses factices terminée !\n";
