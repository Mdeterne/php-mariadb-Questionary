<?php
session_start();
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/src/Models/questionnaire.php';

$model = new questionnaire();
$id = 1;
$userId = 1;
$status = 'closed';
$settings = json_encode(['test' => 'value']);

echo "Updating survey $id to status $status...\n";
$result = $model->updateSurveySettings($id, $userId, $status, $settings);

if ($result) {
    echo "Update successful.\n";
} else {
    echo "Update failed.\n";
}

$survey = $model->getSurveyById($id);
echo "New status: " . $survey['status'] . "\n";
