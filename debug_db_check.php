<?php
// Simple dump to stdout
require_once __DIR__ . '/src/Models/Database.php';

$pdo = (new Database())->getConnection();

echo "--- CHECKING RESPONSES ---\n";
// Count total answers
$stmt = $pdo->query("SELECT COUNT(*) FROM answers");
echo "Total Rows in Answers Table: " . $stmt->fetchColumn() . "\n";

// Check sample text values in ANSWERS
$stmt = $pdo->query("SELECT id, text_value, question_id FROM answers WHERE text_value IS NOT NULL AND text_value != '' LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Sample Text Values in Answers:\n";
print_r($rows);

// Check if survey 18 has questions
$stmt = $pdo->query("SELECT id, type, label FROM questions WHERE survey_id = 18");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Questions for Survey 18:\n";
print_r($questions);

foreach ($questions as $q) {
    echo "Answers for Q " . $q['id'] . " (" . $q['type'] . "):\n";
    // Count matches in ANSWERS table
    $stmt = $pdo->prepare("SELECT text_value FROM answers WHERE question_id = ?");
    $stmt->execute([$q['id']]);
    $answers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "  Total Count: " . count($answers) . "\n";
    echo "  Sample: " . implode(", ", array_slice($answers, 0, 3)) . "\n";
}
