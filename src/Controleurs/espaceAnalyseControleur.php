<?php

class espaceAnalyseControleur
{

    function index()
    {
        // 1. Get ID from URL
        $surveyId = $_GET['id'] ?? null;
        if (!$surveyId) {
            echo "ID du questionnaire manquant.";
            return;
        }

        // 2. Load Models
        require_once(__DIR__ . '/../Models/questionnaire.php');
        require_once(__DIR__ . '/../Models/reponse.php');

        $questionnaireModel = new questionnaire();
        $reponseModel = new Reponse();

        // 3. Check if survey exists (and is active/valid) using a method that gets by ID directly
        // The existing 'exists($pin)' method uses PIN, we need ID.
        // But 'listerLesQuestions' takes a PIN... wait.
        // Let's check 'getSurveysByUserId' results to see if we can just query by ID.
        // Actually the model doesn't have a simple 'getById'. 
        // I will implement a quick fetch manually or assume we can add a method if needed.
        // For now, let's use a direct DB query or add a method to questionnaire.php.
        // Looking at questionnaire.php, 'exists($pin)' selects by PIN.
        // I should probably add 'getById($id)' to questionnaire.php to be clean.
        // BUT, I can just rely on 'listerLesQuestions' if it took an ID... but it takes a PIN.
        
        // Wait, 'listerLesQuestions($pin)' uses 'exists($pin)' which queries by 'access_pin'.
        // The dashboard links pass 'id'.
        // So I need a way to get the survey by ID.
        
        // Let's add 'getSurveyById($id)' to questionnaire model?
        // Or I can query the DB here? No, models only.
        
        // NOTE: The previous flow seemed to use PIN for public access. 
        // For the creator, we have the ID.
        
        // Let's assume for this step I will ALSO update questionnaire.php to add 'getSurveyById'
        // But first let's see if I can just use 'listerLesQuestions' by retrieving the PIN first?
        // No, that's inefficient.
        
        // Let's try to query via a new method. I'll insert it into the View directly if I can't modify the model easily?
        // No, I should modify the model.
        
        // Actually, looking at 'listerLesQuestions', it gets ID from PIN.
        // I need 'listerLesQuestionsById($id)'.
        
        // Let's modify the controller to include the necessary logic, 
        // assuming I will update the Model in the next step to support 'getSurveyById'.
        
        // Wait, I can't leave the controller broken.
        // I will implement the logic to fetch data using a new method 'getFullSurveyById' that I will add to the model.
        
        // ...Wait, I'll modify the Model first?
        // Plan said: "Fetch questionnaire title/status via questionnaire->exists() (or new method getById)."
        
        // So I will write the controller assuming 'getById' exists.
        
        $survey = $questionnaireModel->getSurveyById($surveyId);
        
        if (!$survey) {
            echo "Questionnaire introuvable.";
            return;
        }

        // 4. Get Stats
        $responseCount = $reponseModel->getTotalResponsesCount($surveyId);
        
        // 5. Get Questions and Answers
        // The existing 'listerLesQuestions' gets simple questions.
        // I need to enrich them with stats.
        
        // Let's reuse 'listerLesQuestionsById' logic.
        // Since I'm in the controller, I'll basically do what 'listerLesQuestions' does but with ID.
        // Actually, 'listerLesQuestions' does ALMOST what I want but with PIN.
        
        // I'll call a new method 'getAnalysisData($id)' on the model which I'll create.
        // This is cleaner.
        
        $analysisData = $questionnaireModel->getAnalysisData($surveyId);
        
        // Inject stats into the questions
        // $analysisData['questions'] is the array.
        
        foreach ($analysisData['questions'] as &$question) {
             if (in_array($question['type'], ['single_choice', 'multiple_choice'])) {
                 $question['stats'] = $reponseModel->getQuestionStats($question['id']);
             }
             // For text answers?
             if ($question['type'] === 'text' || $question['type'] === 'paragraph') {
                  $question['text_answers'] = $reponseModel->getTextAnswers($question['id']);
             }
        }
        
        // 6. View Data
        $pageTitle = $survey['title'];
        $questionsData = json_encode($analysisData['questions']);
        
        require_once(__DIR__ . '/../Views/analyse/analyse.php');
    }
}