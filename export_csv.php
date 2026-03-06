<?php
require_once 'src/Modeles/Database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erreur: ID du questionnaire manquant.");
}
$surveyId = $_GET['id'];

try {
    $database = new Database();
    $conn = $database->getConnection();
} catch (Exception $e) {
    die("Erreur de connexion base de données");
}

// Récupération des données du questionnaire
$stmtSurvey = $conn->prepare("SELECT * FROM surveys WHERE id = :id");
$stmtSurvey->execute([':id' => $surveyId]);
$survey = $stmtSurvey->fetch(PDO::FETCH_ASSOC);

if (!$survey) die("Questionnaire introuvable.");

// Récupération des questions, ordonnées
$stmtQuestions = $conn->prepare("SELECT id, type, label FROM questions WHERE survey_id = :sid ORDER BY order_index ASC");
$stmtQuestions->execute([':sid' => $surveyId]);
$questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

$questionIds = array_column($questions, 'id');
$questionLabels = array_column($questions, 'label');

// Préparation du fichier CSV
$filename = "export_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', strtolower($survey['title'])) . "_" . date('Y-md_His') . ".csv";

// Headers HTTP pour forcer le téléchargement
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Ouverture du flux de sortie
$output = fopen('php://output', 'w');

// Ajout du BOM UTF-8 pour la compatibilité Excel
fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

// En-têtes du CSV
$headerCsv = ['ID Réponse', 'Date de soumission'];
foreach ($questionLabels as $label) {
    $headerCsv[] = $label;
}
fputcsv($output, $headerCsv, ';', '"', '\\'); // Utilisation du point-virgule pour Excel francophone, et on définit explicitement enclosure et escape pour éviter le warning PHP 8.4

// Récupération des réponses
$stmtResponses = $conn->prepare("SELECT id, user_id, submitted_at FROM responses WHERE survey_id = :sid ORDER BY submitted_at ASC");
$stmtResponses->execute([':sid' => $surveyId]);
$responses = $stmtResponses->fetchAll(PDO::FETCH_ASSOC);

foreach ($responses as $response) {
    $row = [
        $response['id'],
        $response['submitted_at']
    ];

    // Pour chaque question, trouver la réponse
    foreach ($questions as $q) {
        $qId = $q['id'];
        
        // Requête pour récupérer la réponse
        $stmtAns = $conn->prepare("SELECT id, text_value FROM answers WHERE response_id = :rid AND question_id = :qid");
        $stmtAns->execute([':rid' => $response['id'], ':qid' => $qId]);
        $answer = $stmtAns->fetch(PDO::FETCH_ASSOC);

        if (!$answer) {
            $row[] = ''; // Pas de réponse
            continue;
        }

        if (in_array($q['type'], ['short_text', 'long_text', 'scale', 'text', 'paragraph'])) {
            $row[] = $answer['text_value'];
        } else {
            // Choix multiple / unique : récupérer les labels des options
            $stmtChoices = $conn->prepare("
                SELECT qo.label 
                FROM answer_choices ac
                JOIN question_options qo ON ac.option_id = qo.id
                WHERE ac.answer_id = :aid
            ");
            $stmtChoices->execute([':aid' => $answer['id']]);
            $choices = $stmtChoices->fetchAll(PDO::FETCH_COLUMN);
            
            // On gère aussi le text_value si jamais y a "Autre"
            $allChoices = implode(', ', $choices);
            if (!empty($answer['text_value'])) {
                $allChoices .= ($allChoices ? ', ' : '') . $answer['text_value'];
            }
            
            $row[] = $allChoices;
        }
    }
    
    fputcsv($output, $row, ';', '"', '\\');
}

fclose($output);
exit();
