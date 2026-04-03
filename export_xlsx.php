<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require 'vendor/autoload.php';
require_once 'src/Modeles/Database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erreur: ID du questionnaire manquant.");
}
$surveyId = $_GET['id'];
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;

try {
    $database = new Database();
    $conn = $database->getConnection();
} catch (Exception $e) {
    die("Erreur de connexion base de données");
}

// 1. Récupération des infos du questionnaire
$stmtSurvey = $conn->prepare("SELECT * FROM surveys WHERE id = :id");
$stmtSurvey->execute([':id' => $surveyId]);
$survey = $stmtSurvey->fetch(PDO::FETCH_ASSOC);
if (!$survey) die("Questionnaire introuvable.");

// 2. Récupération des questions
$stmtQuestions = $conn->prepare("SELECT id, type, label FROM questions WHERE survey_id = :sid ORDER BY order_index ASC");
$stmtQuestions->execute([':sid' => $surveyId]);
$questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

// 3. Chargement du template
$templatePath = __DIR__ . DIRECTORY_SEPARATOR . 'template.xlsx';
if (!file_exists($templatePath)) {
    die("Erreur: Le fichier template.xlsx est introuvable à la racine du projet.");
}

try {
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();
} catch (Exception $e) {
    die("Erreur lors du chargement du template : " . $e->getMessage());
}

// 4. Récupération des réponses avec filtres optionnels
$sqlResp = "SELECT id, submitted_at FROM responses WHERE survey_id = :sid AND submitted_at IS NOT NULL";
$pResp = [':sid' => $surveyId];

if ($startDate) {
    $sqlResp .= " AND DATE(submitted_at) >= :sd";
    $pResp[':sd'] = $startDate;
}
if ($endDate) {
    $sqlResp .= " AND DATE(submitted_at) <= :ed";
    $pResp[':ed'] = $endDate;
}

$sqlResp .= " ORDER BY submitted_at ASC";
$stmtR = $conn->prepare($sqlResp);
$stmtR->execute($pResp);
$responses = $stmtR->fetchAll(PDO::FETCH_ASSOC);

// 5. Remplissage des en-têtes à la ligne 10
$sheet->setCellValue('A10', 'ID Réponse');
$sheet->setCellValue('B10', 'Date de soumission');
$headerColIdx = 2;
foreach ($questions as $q) {
    $colLetter = Coordinate::stringFromColumnIndex($headerColIdx + 1);
    $sheet->setCellValue($colLetter . '10', $q['label']);
    $headerColIdx++;
}

// 6. Remplissage des données à partir de la ligne 11
$rowIdx = 11;
foreach ($responses as $resp) {
    // Colonne A : ID Réponse
    $sheet->setCellValue('A' . $rowIdx, $resp['id']);
    // Colonne B : Date de soumission
    $sheet->setCellValue('B' . $rowIdx, $resp['submitted_at']);
    
    // Colonnes suivantes : Réponses aux questions
    $colIdx = 2; // Index 2 correspond à la colonne C
    foreach ($questions as $q) {
        $stmtA = $conn->prepare("SELECT id, text_value FROM answers WHERE response_id = :rid AND question_id = :qid");
        $stmtA->execute([':rid' => $resp['id'], ':qid' => $q['id']]);
        $ans = $stmtA->fetch(PDO::FETCH_ASSOC);
        
        $val = "";
        if ($ans) {
            // Mapping des types pour récupérer les labels des options
            if (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
                $stmtC = $conn->prepare("SELECT qo.label FROM answer_choices ac JOIN question_options qo ON ac.option_id = qo.id WHERE ac.answer_id = :aid");
                $stmtC->execute([':aid' => $ans['id']]);
                $choices = $stmtC->fetchAll(PDO::FETCH_COLUMN);
                $val = implode(', ', $choices);
                // Inclure la valeur texte si c'est une option "Autre"
                if (!empty($ans['text_value'])) {
                    $val .= ($val ? ', ' : '') . $ans['text_value'];
                }
            } else {
                $val = $ans['text_value'];
            }
        }
        
        $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
        $sheet->setCellValue($colLetter . $rowIdx, $val);
        $colIdx++;
    }
    $rowIdx++;
}

// === Ajustements de style et de taille ===
// On détermine la dernière colonne utilisée
$lastColIdx = 2 + count($questions);
$lastColLetter = Coordinate::stringFromColumnIndex($lastColIdx);

// 1. Auto-size pour les colonnes ID et Date
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);

// 2. Largeur fixe et wrap text pour les questions/réponses (Colonnes C à fin)
for ($i = 3; $i <= $lastColIdx; $i++) {
    $col = Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($col)->setWidth(35); // Largeur confortable
}

// 3. Activation du retour à la ligne pour toute la zone de données (ligne 10 à fin)
$sheet->getStyle('A10:' . $lastColLetter . ($rowIdx - 1))
      ->getAlignment()
      ->setWrapText(true)
      ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

// 7. Finalisation et envoi du fichier
$cleanTitle = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $survey['title']);
$filename = "Export_Template_" . $cleanTitle . "_" . date('Y-m-d') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
