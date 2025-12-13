<?php
// Debug script to dump analysis data to JSON
require_once __DIR__ . '/src/Models/questionnaire.php';
require_once __DIR__ . '/src/Models/reponse.php';

$model = new Questionnaire();
$surveyId = 18; // Target survey ID
$data = $model->getAnalysisData($surveyId);

// Add stats and text answers
$reponseModel = new Reponse();
foreach ($data as &$question) {
    if (in_array($question['type'], ['text', 'short_text', 'long_text', 'paragraph'])) {
        $question['text_answers'] = $reponseModel->getTextAnswers($question['id']);
    } else {
        $question['stats'] = $reponseModel->getQuestionStats($question['id'], $question['type']);
    }
}

file_put_contents('debug_output.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Data dumped to debug_output.json";
