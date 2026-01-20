<?php
require_once __DIR__ . '/src/Modeles/Database.php';
require_once __DIR__ . '/src/Modeles/Reponse.php';

// Mock data
$surveyId = 1; // Assuming survey ID 1 exists
// Don't hardcode mock data yet, wait for valid IDs


try {
    $db = new Database();
    $conn = $db->getConnection();
    if (!$conn) {
        echo "Error: Database connection failed.\n";
        exit;
    } else {
        echo "Database connection successful.\n";
    }

    // Check if survey 1 exists
    $stmt = $conn->query("SELECT id FROM surveys WHERE id = $surveyId");
    if (!$stmt || $stmt->rowCount() == 0) {
        echo "Error: Survey ID $surveyId does not exist. Creating it...\n";
        $conn->exec("INSERT INTO surveys (id, title, description, created_by) VALUES (1, 'Test Survey', 'Desc', 1)"); 
    }

    // Ensure we have questions
    $stmtQ = $conn->query("SELECT id, type FROM questions WHERE survey_id = $surveyId");
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);
    if (empty($questions)) {
        echo "Creating questions...\n";
        $conn->exec("INSERT INTO questions (id, survey_id, label, type, order_index) VALUES (101, 1, 'Question Texte', 'short_text', 1)");
        $conn->exec("INSERT INTO questions (id, survey_id, label, type, order_index) VALUES (102, 1, 'Question Choix', 'single_choice', 2)");
        $conn->exec("INSERT INTO question_options (id, question_id, label, order_index) VALUES (201, 102, 'Option A', 1)");
        $questions = [
            ['id' => 101, 'type' => 'short_text'],
            ['id' => 102, 'type' => 'single_choice']
        ];
    }

    $answers = [];
    foreach ($questions as $q) {
        if ($q['type'] == 'short_text') {
            $answers[$q['id']] = "Test Response " . time();
        } elseif ($q['type'] == 'single_choice') {
            // Get an option
            $stmtO = $conn->query("SELECT id FROM question_options WHERE question_id = " . $q['id']);
            $opt = $stmtO->fetchColumn();
            if ($opt) {
                $answers[$q['id']] = $opt;
            } else {
                 // Create option if missing
                 $conn->exec("INSERT INTO question_options (question_id, label, order_index) VALUES (".$q['id'].", 'Opt', 1)");
                 $id = $conn->lastInsertId();
                 $answers[$q['id']] = $id;
            }
        }
    }
    
    echo "Testing with payload: " . json_encode($answers) . "\n";


    $reponseModel = new Reponse();
    // Temporarily modify Reponse logic in memory or just try/catch here isn't enough because Reponse swallows it.
    // Instead we will rely on Reponse returning false.
    
    // We can't easily modify the class instance method to throw.
    // So let's try to replicate the insert manually to see the error, OR enable error logging if possible.
    
    $result = $reponseModel->saveFullResponse($surveyId, $answers);
    
    if ($result) {
        echo "Success: Response saved.\n";
    } else {
        echo "Failure: Response not saved. (Likely swallowed exception in Reponse.php)\n";
        // Manual debug of the query
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO responses (survey_id, submitted_at) VALUES (:survey_id, NOW())");
            $stmt->execute([':survey_id' => $surveyId]);
            $id = $conn->lastInsertId();
            echo "Manual Insert Test: Success, ID = $id\n";
            $conn->rollBack();
        } catch (Exception $e) {
            echo "Manual Insert Test: Failed - " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Exception Caught: " . $e->getMessage() . "\n";
}
