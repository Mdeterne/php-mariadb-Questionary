<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require 'vendor/autoload.php';
require_once 'src/Modeles/Database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

$stmtSurvey = $conn->prepare("SELECT * FROM surveys WHERE id = :id");
$stmtSurvey->execute([':id' => $surveyId]);
$survey = $stmtSurvey->fetch(PDO::FETCH_ASSOC);

if (!$survey) die("Questionnaire introuvable.");

$stmtQuestions = $conn->prepare("SELECT id, type, label FROM questions WHERE survey_id = :sid ORDER BY order_index ASC");
$stmtQuestions->execute([':sid' => $surveyId]);
$questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

$questionIds = array_column($questions, 'id');
$questionLabels = array_column($questions, 'label');

// Chemin vers le template
$templatePath = __DIR__ . '/template.xlsx';
if (!file_exists($templatePath)) {
    die("Erreur : Le fichier template 'template.xlsx' est introuvable à la racine du projet.");
}

// Charger le template existant
$spreadsheet = IOFactory::load($templatePath);
$sheet = $spreadsheet->getActiveSheet();

// La ligne de début pour les données (la ligne 4 est utilisée pour les headers, 5 pour les données)
// Ceci permet de garder l'en-tête du template intact sur les 3 premières lignes
$startRow = 10;

// En-têtes du fichier Excel (Ligne 4)
$headerExcel = ['ID Réponse', 'Date de soumission'];
foreach ($questionLabels as $label) {
    $headerExcel[] = $label;
}

$colIndex = 1;
foreach ($headerExcel as $headerTitle) {
    $sheet->setCellValueExplicit([$colIndex, $startRow], $headerTitle, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    // Petit style basique pour l'entête même avec le template
    $sheet->getStyle([$colIndex, $startRow])->getFont()->setBold(true);
    $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
    $colIndex++;
}

// Récupération des réponses
$stmtResponses = $conn->prepare("SELECT id, user_id, submitted_at FROM responses WHERE survey_id = :sid ORDER BY submitted_at ASC");
$stmtResponses->execute([':sid' => $surveyId]);
$responses = $stmtResponses->fetchAll(PDO::FETCH_ASSOC);

// Insertion des données (Ligne 5 et +)
$rowIndex = $startRow + 1;
foreach ($responses as $response) {
    $row = [
        $response['id'],
        $response['submitted_at']
    ];

    foreach ($questions as $q) {
        $qId = $q['id'];
        $stmtAns = $conn->prepare("SELECT id, text_value FROM answers WHERE response_id = :rid AND question_id = :qid");
        $stmtAns->execute([':rid' => $response['id'], ':qid' => $qId]);
        $answer = $stmtAns->fetch(PDO::FETCH_ASSOC);

        if (!$answer) {
            $row[] = '';
            continue;
        }

        if (in_array($q['type'], ['short_text', 'long_text', 'scale', 'text', 'paragraph'])) {
            $row[] = $answer['text_value'];
        } else {
            $stmtChoices = $conn->prepare("
                SELECT qo.label 
                FROM answer_choices ac
                JOIN question_options qo ON ac.option_id = qo.id
                WHERE ac.answer_id = :aid
            ");
            $stmtChoices->execute([':aid' => $answer['id']]);
            $choices = $stmtChoices->fetchAll(PDO::FETCH_COLUMN);
            $allChoices = implode(', ', $choices);
            if (!empty($answer['text_value'])) {
                $allChoices .= ($allChoices ? ', ' : '') . $answer['text_value'];
            }
            $row[] = $allChoices;
        }
    }
    
    $colIndex = 1;
    foreach ($row as $cellValue) {
        $sheet->setCellValueExplicit([$colIndex, $rowIndex], (string) $cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $colIndex++;
    }
    $rowIndex++;
}

// Fichier final
$filename = "export_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', strtolower($survey['title'])) . "_" . date('Y-md_His') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
