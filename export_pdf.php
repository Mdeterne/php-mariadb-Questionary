<?php
require 'vendor/autoload.php';
require_once 'src/Modeles/Database.php';

use Dompdf\Dompdf;
use Dompdf\Options; // Import des options

// Style CSS pour le PDF
$css = '
<style>
    body { font-family: sans-serif; font-size: 12px; color: #333; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #b52424; padding-bottom: 10px; }
    .logo { position: absolute; top: 0; right: 0; width: 150px; }
    h1 { margin: 0; color: #b52424; font-size: 24px; }
    .description { font-style: italic; color: #666; margin-top: 5px; }
    .question { margin-bottom: 20px; page-break-inside: avoid; }
    .question-title { font-weight: bold; font-size: 14px; margin-bottom: 5px; background-color: #f8f9fa; padding: 5px; border-left: 4px solid #b52424; }
    .response-area { border: 1px solid #ddd; height: 100px; margin-top: 5px; background-color: #fff; }
    .options-list { list-style-type: none; padding-left: 0; }
    .options-list li { margin-bottom: 5px; }
    .checkbox-box { display: inline-block; width: 12px; height: 12px; border: 1px solid #333; margin-right: 5px; vertical-align: middle; }
    .radio-circle { display: inline-block; width: 12px; height: 12px; border: 1px solid #333; border-radius: 50%; margin-right: 5px; vertical-align: middle; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; text-align: center; color: #aaa; }
</style>';

// Vérifions d'abord si l'ID est bien fourni pour éviter les erreurs
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erreur: ID du questionnaire manquant.");
}
$surveyId = $_GET['id'];

// Initialisation de la connexion sécurisée via PDO
try {
    $database = new Database();
    $conn = $database->getConnection();
} catch (Exception $e) {
    die("Erreur de connexion base de données");
}

// Récupération des données du questionnaire et des questions associées
$stmtSurvey = $conn->prepare("SELECT * FROM surveys WHERE id = :id");
$stmtSurvey->execute([':id' => $surveyId]);
$survey = $stmtSurvey->fetch(PDO::FETCH_ASSOC);

if (!$survey) die("Questionnaire introuvable.");

$stmtQuestions = $conn->prepare("SELECT * FROM questions WHERE survey_id = :sid ORDER BY order_index ASC");
$stmtQuestions->execute([':sid' => $surveyId]);
$questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);


// Boucle de génération des questions
$content = '';
foreach ($questions as $idx => $q) {
    $content .= '<div class="question">';
    $content .= '<div class="question-title">Q' . ($idx + 1) . '. ' . htmlspecialchars($q['label']) . '</div>';
    
    // Logique conditionnelle selon le type de question
    if (in_array($q['type'], ['text', 'paragraph', 'long_text'])) {
        $content .= '<div class="response-area"></div>';
    } elseif (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
        // Récupération des options pour les QCM
        $stmtOpt = $conn->prepare("SELECT * FROM question_options WHERE question_id = ? ORDER BY order_index");
        $stmtOpt->execute([$q['id']]);
        $content .= '<ul class="options-list">';
        foreach ($stmtOpt->fetchAll() as $opt) {
            $content .= '<li><span class="checkbox-box"></span> ' . htmlspecialchars($opt['label']) . '</li>';
        }
        $content .= '</ul>';
    }

// Assemblage et rendu final PDF
$fullHtml = '<html><head>' . $css . '</head><body>' . $headerHtml . $content . '</body></html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'portrait');

$dompdf->loadHtml($fullHtml);
$dompdf->render();
$dompdf->stream("questionnaire-{$surveyId}.pdf", ["Attachment" => true]);







// Gestion des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du questionnaire manquant.");
}

$surveyId = $_GET['id'];

// Initialiser la connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

// Récupérer les informations du questionnaire
$querySurvey = "SELECT * FROM surveys WHERE id = :id";
$stmtSurvey = $conn->prepare($querySurvey);
$stmtSurvey->bindParam(':id', $surveyId);
$stmtSurvey->execute();
$survey = $stmtSurvey->fetch(PDO::FETCH_ASSOC);

if (!$survey) {
    die("Questionnaire introuvable.");
}

// Récupérer les questions (Implementation DB)
$queryQuestions = "SELECT * FROM questions WHERE survey_id = :survey_id ORDER BY order_index ASC";
$stmtQuestions = $conn->prepare($queryQuestions);
$stmtQuestions->bindParam(':survey_id', $surveyId);
$stmtQuestions->execute();
$questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);
