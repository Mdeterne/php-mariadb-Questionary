<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/src/Models/Database.php';

echo "<h1>Génération de votes supplémentaires...</h1>";

$db = new Database();
$pdo = $db->getConnection();

// Get the latest survey (likely "Sondage Analyse")
$stmt = $pdo->query("SELECT id FROM surveys ORDER BY id DESC LIMIT 1");
$surveyId = $stmt->fetchColumn();

if (!$surveyId) {
    die("Aucun questionnaire trouvé.");
}

echo "Questionnaire ID : $surveyId<br>";

// Get Q1 (Choice)
$stmt = $pdo->prepare("SELECT id FROM questions WHERE survey_id = ? AND type = 'single_choice' LIMIT 1");
$stmt->execute([$surveyId]);
$q1Id = $stmt->fetchColumn();

if (!$q1Id) {
    die("Pas de question à choix trouvée pour ce questionnaire.");
}

// Get Options for Q1
$stmt = $pdo->prepare("SELECT id FROM question_options WHERE question_id = ?");
$stmt->execute([$q1Id]);
$options = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($options)) {
    die("Pas d'options trouvées.");
}

// Add 50 random responses
$total = 50;
for ($i = 0; $i < $total; $i++) {
    // 1. Create Response
    $pdo->prepare("INSERT INTO responses (survey_id, submitted_at) VALUES (?, NOW() - INTERVAL " . rand(0, 10000) . " SECOND)")->execute([$surveyId]);
    $respId = $pdo->lastInsertId();
    
    // 2. Pick Random Option
    $randomOptId = $options[array_rand($options)];
    
    // 3. Create Answer
    $pdo->prepare("INSERT INTO answers (response_id, question_id) VALUES (?, ?)")->execute([$respId, $q1Id]);
    $ansId = $pdo->lastInsertId();
    
    // 4. Link Choice
    $pdo->prepare("INSERT INTO answer_choices (answer_id, option_id) VALUES (?, ?)")->execute([$ansId, $randomOptId]);
}

echo "<h3>$total votes ajoutés avec succès !</h3>";
echo "<a href='?c=espaceAnalyse&id=$surveyId'>Voir l'analyse</a>";
