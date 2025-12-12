<?php
require_once __DIR__ . '/src/Models/Database.php';

$db = new Database();
$pdo = $db->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$surveyId = 1;
$count = 30;

echo "Adding $count responses to Survey ID $surveyId...\n";

// 1. Fetch Questions
$stmt = $pdo->prepare("SELECT id, type FROM questions WHERE survey_id = ? ORDER BY order_index");
$stmt->execute([$surveyId]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch Options for all questions
$optionsByQuestion = [];
$stmtOpt = $pdo->prepare("SELECT id, question_id FROM question_options WHERE question_id = ?");

foreach ($questions as $q) {
    if (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
        $stmtOpt->execute([$q['id']]);
        $optionsByQuestion[$q['id']] = $stmtOpt->fetchAll(PDO::FETCH_COLUMN);
    }
}

$fakerNames = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Heidi', 'Ivan', 'Judy'];
$fakerText = ['Ceci est une réponse.', 'Je suis très content.', 'Pas terrible.', 'A revoir.', 'Excellent service !', 'Moyen.', 'Pourquoi pas ?', 'Je ne sais pas.', 'Super !', 'Bof.'];

for ($i = 0; $i < $count; $i++) {
    // Create Response
    $stmt = $pdo->prepare("INSERT INTO responses (survey_id, submitted_at) VALUES (?, NOW())");
    $stmt->execute([$surveyId]);
    $responseId = $pdo->lastInsertId();

    foreach ($questions as $q) {
        $qId = $q['id'];
        $type = $q['type'];
        
        $textValue = null;
        $selectedOptions = [];

        if ($type === 'short_text') {
            $textValue = $fakerNames[array_rand($fakerNames)];
        } elseif ($type === 'long_text') {
            $textValue = $fakerText[array_rand($fakerText)];
        } elseif ($type === 'scale') {
            $textValue = rand(1, 5);
        } elseif ($type === 'single_choice') {
            $opts = $optionsByQuestion[$qId] ?? [];
            if (!empty($opts)) {
                $selectedOptions[] = $opts[array_rand($opts)];
            }
        } elseif ($type === 'multiple_choice') {
            $opts = $optionsByQuestion[$qId] ?? [];
            if (!empty($opts)) {
                shuffle($opts);
                $selectedOptions = array_slice($opts, 0, rand(1, count($opts)));
            }
        }

        // Insert Answer
        $stmtAns = $pdo->prepare("INSERT INTO answers (response_id, question_id, text_value) VALUES (?, ?, ?)");
        $stmtAns->execute([$responseId, $qId, $textValue]);
        $answerId = $pdo->lastInsertId();

        // Insert Choices
        if (!empty($selectedOptions)) {
            $stmtChoice = $pdo->prepare("INSERT INTO answer_choices (answer_id, option_id) VALUES (?, ?)");
            foreach ($selectedOptions as $optId) {
                $stmtChoice->execute([$answerId, $optId]);
            }
        }
    }
}

echo "Done. Added $count responses.\n";
