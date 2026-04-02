<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
require 'vendor/autoload.php';
require_once 'src/Modeles/Database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

$spreadsheet = new Spreadsheet();

// --- FEUILLE 1 : RÉSUMÉ DES STATISTIQUES ---
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('RÉSUMÉ');

$sheet1->setCellValue('A1', 'RÉSUMÉ DES RÉSULTATS : ' . $survey['title']);
$sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet1->mergeCells('A1:D1');

$filterText = "Période : " . ($startDate ? "du $startDate " : "le début ") . ($endDate ? "au $endDate" : "à aujourd'hui");
$sheet1->setCellValue('A2', $filterText);
$sheet1->getStyle('A2')->getFont()->setItalic(true);

// Nombre total de réponses filtrées
$sqlRespCount = "SELECT COUNT(*) FROM responses WHERE survey_id = :sid AND submitted_at IS NOT NULL";
$params = [':sid' => $surveyId];
if ($startDate) { $sqlRespCount .= " AND DATE(submitted_at) >= :sd"; $params[':sd'] = $startDate; }
if ($endDate) { $sqlRespCount .= " AND DATE(submitted_at) <= :ed"; $params[':ed'] = $endDate; }
$stmtCount = $conn->prepare($sqlRespCount);
$stmtCount->execute($params);
$totalResponses = $stmtCount->fetchColumn();

$sheet1->setCellValue('A4', 'Total des réponses :');
$sheet1->setCellValue('B4', $totalResponses);
$sheet1->getStyle('A4')->getFont()->setBold(true);

$row = 6;
foreach ($questions as $q) {
    $sheet1->setCellValue('A' . $row, $q['label']);
    $sheet1->getStyle('A' . $row)->getFont()->setBold(true);
    $sheet1->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');
    $row++;

    if (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
        $sqlStats = "SELECT qo.label, COUNT(ac.id) as count 
                     FROM question_options qo 
                     LEFT JOIN answer_choices ac ON qo.id = ac.option_id 
                     LEFT JOIN answers ans ON ac.answer_id = ans.id
                     LEFT JOIN responses r ON ans.response_id = r.id
                     WHERE qo.question_id = :qid";
        $p = [':qid' => $q['id']];
        if ($startDate) { $sqlStats .= " AND DATE(r.submitted_at) >= :sd"; $p[':sd'] = $startDate; }
        if ($endDate) { $sqlStats .= " AND DATE(r.submitted_at) <= :ed"; $p[':ed'] = $endDate; }
        $sqlStats .= " GROUP BY qo.id, qo.label ORDER BY qo.order_index ASC";
        
        $stmtS = $conn->prepare($sqlStats);
        $stmtS->execute($p);
        $stats = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        foreach ($stats as $s) {
            $sheet1->setCellValue('B' . $row, $s['label']);
            $sheet1->setCellValue('C' . $row, $s['count']);
            $percent = $totalResponses > 0 ? ($s['count'] / $totalResponses) : 0;
            $sheet1->setCellValue('D' . $row, $percent);
            $sheet1->getStyle('D' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
            $row++;
        }
    } elseif ($q['type'] === 'scale' || $q['type'] === 'jauge') {
        $sqlScale = "SELECT text_value as label, COUNT(*) as count 
                     FROM answers a 
                     JOIN responses r ON a.response_id = r.id
                     WHERE a.question_id = :qid";
        $p = [':qid' => $q['id']];
        if ($startDate) { $sqlScale .= " AND DATE(r.submitted_at) >= :sd"; $p[':sd'] = $startDate; }
        if ($endDate) { $sqlScale .= " AND DATE(r.submitted_at) <= :ed"; $p[':ed'] = $endDate; }
        $sqlScale .= " GROUP BY text_value ORDER BY CAST(text_value AS UNSIGNED) ASC";
        
        $stmtS = $conn->prepare($sqlScale);
        $stmtS->execute($p);
        $stats = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        $sum = 0; $count = 0;
        foreach ($stats as $s) {
            $sheet1->setCellValue('B' . $row, "Note : " . $s['label']);
            $sheet1->setCellValue('C' . $row, $s['count']);
            $sum += (int)$s['label'] * $s['count'];
            $count += $s['count'];
            $row++;
        }
        if ($count > 0) {
            $sheet1->setCellValue('B' . $row, "MOYENNE :");
            $sheet1->setCellValue('C' . $row, round($sum / $count, 2));
            $sheet1->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;
        }
    } else {
        $sheet1->setCellValue('B' . $row, "(Réponses textuelles - voir onglet Analyse Qualitative)");
        $row++;
    }
    $row++;
}
foreach (range('A','D') as $col) { $sheet1->getColumnDimension($col)->setAutoSize(true); }

// --- FEUILLE 2 : RÉPONSES DÉTAILLÉES ---
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('RÉPONSES DÉTAILLÉES');

$headers = ['ID Réponse', 'Date'];
foreach ($questions as $q) { $headers[] = $q['label']; }

$col = 'A';
foreach ($headers as $h) {
    $sheet2->setCellValue($col . '1', $h);
    $sheet2->getStyle($col . '1')->getFont()->setBold(true);
    $sheet2->getStyle($col . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');
    $col++;
}

$sqlResp = "SELECT id, submitted_at FROM responses WHERE survey_id = :sid AND submitted_at IS NOT NULL";
$pResp = [':sid' => $surveyId];
if ($startDate) { $sqlResp .= " AND DATE(submitted_at) >= :sd"; $pResp[':sd'] = $startDate; }
if ($endDate) { $sqlResp .= " AND DATE(submitted_at) <= :ed"; $pResp[':ed'] = $endDate; }
$sqlResp .= " ORDER BY submitted_at ASC";
$stmtR = $conn->prepare($sqlResp);
$stmtR->execute($pResp);
$responses = $stmtR->fetchAll(PDO::FETCH_ASSOC);

$rowIdx = 2;
foreach ($responses as $resp) {
    $sheet2->setCellValue('A' . $rowIdx, $resp['id']);
    $sheet2->setCellValue('B' . $rowIdx, $resp['submitted_at']);
    
    $colIdx = 2; // Start from column C (index 2)
    foreach ($questions as $q) {
        $stmtA = $conn->prepare("SELECT id, text_value FROM answers WHERE response_id = :rid AND question_id = :qid");
        $stmtA->execute([':rid' => $resp['id'], ':qid' => $q['id']]);
        $ans = $stmtA->fetch(PDO::FETCH_ASSOC);
        
        $val = "";
        if ($ans) {
            if (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
                $stmtC = $conn->prepare("SELECT qo.label FROM answer_choices ac JOIN question_options qo ON ac.option_id = qo.id WHERE ac.answer_id = :aid");
                $stmtC->execute([':aid' => $ans['id']]);
                $choices = $stmtC->fetchAll(PDO::FETCH_COLUMN);
                $val = implode(', ', $choices);
                if (!empty($ans['text_value'])) $val .= ($val ? ', ' : '') . $ans['text_value'];
            } else {
                $val = $ans['text_value'];
            }
        }
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
        $sheet2->setCellValue($colLetter . $rowIdx, $val);
        $colIdx++;
    }
    $rowIdx++;
}

// --- FEUILLE 3 : ANALYSE QUALITATIVE ---
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('ANALYSE QUALITATIVE');

$row = 1;
foreach ($questions as $q) {
    if (in_array($q['type'], ['text', 'paragraph', 'short_text', 'long_text'])) {
        $sheet3->setCellValue('A' . $row, $q['label']);
        $sheet3->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;

        $sqlTxt = "SELECT a.text_value FROM answers a JOIN responses r ON a.response_id = r.id WHERE a.question_id = :qid AND a.text_value != ''";
        $p = [':qid' => $q['id']];
        if ($startDate) { $sqlTxt .= " AND DATE(r.submitted_at) >= :sd"; $p[':sd'] = $startDate; }
        if ($endDate) { $sqlTxt .= " AND DATE(r.submitted_at) <= :ed"; $p[':ed'] = $endDate; }
        
        $stmtT = $conn->prepare($sqlTxt);
        $stmtT->execute($p);
        $texts = $stmtT->fetchAll(PDO::FETCH_COLUMN);

        if (empty($texts)) {
            $sheet3->setCellValue('A' . $row, "(Aucune réponse)");
            $row++;
        } else {
            foreach ($texts as $t) {
                $sheet3->setCellValue('A' . $row, "- " . $t);
                $sheet3->getStyle('A' . $row)->getAlignment()->setWrapText(true);
                $row++;
            }
        }
        $row += 2;
    }
}
$sheet3->getColumnDimension('A')->setWidth(100);

// Finalisation
$spreadsheet->setActiveSheetIndex(0);
$filename = "Analyse_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $survey['title']) . "_" . date('Y-m-d') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
