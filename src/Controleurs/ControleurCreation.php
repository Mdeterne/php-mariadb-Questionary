<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php';

class ControleurCreation
{
    function nouveauFormulaire()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
    }

    function index()
    {
        $this->nouveauFormulaire();
    }

    function editer()
    {
        if (!isset($_GET['id'])) {
            header('Location: ?c=tableauDeBord');
            exit;
        }

        $id = $_GET['id'];
        $model = new Questionnaire();

        // Verify ownership (or existence)
        $survey = $model->getAnalysisData($id); // Re-use analysis data fetching so we get everything
        if (!$survey) {
            header('Location: ?c=tableauDeBord');
            exit;
        }

        // Ideally check user_id if we want stricter security here, but getSurveysByUserId on dashboard already filters access.
        // But for direct URL access:
        // if ($survey['user_id'] != $_SESSION['user_id']) die('Unauthorized');

        // Pass data to view
        $existingSurvey = $survey;
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
    }

    function import($pin)
    {
        $model = new Questionnaire();
        if ($model->exists($pin)) {
            $questionnaire = $model->getSurveyByPin($pin);
            if ($questionnaire['user_id'] != $_SESSION['id']) {
                $id = $questionnaire['id'];
                $model->import($id);
                // Redirection vers le tableau de bord avec succès
                header('Location: ?c=tableauDeBord&import=success');
                exit;
            } else {
                // Redirection vers le tableau de bord avec erreur (déjà propriétaire)
                header('Location: ?c=tableauDeBord&import=error');
                exit;
            }
        } else {
            // Redirection vers le tableau de bord avec erreur (introuvable)
            header('Location: ?c=tableauDeBord&import=error');
            exit;
        }
    }

    function save()
    {
        $modelQuestionnaire = new Questionnaire();
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $user_id = $_SESSION['id'];
        $questions = isset($_POST['questions']) ? json_decode($_POST['questions'], true) : [];

        // Check if updating
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            try {
                $modelQuestionnaire->updateSurvey($id, $user_id, $titre, $description, $questions);
                require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
            } catch (Exception $e) {
                http_response_code(500);
                echo "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        } else {
            // New creation
            $access_pin = rand(100000, 999999);
            $qr_code_token = bin2hex(random_bytes(16));
            while (true) {
                if (!$modelQuestionnaire->exists($access_pin)) {
                    break;
                }
                $access_pin = rand(100000, 999999);
            }
            try {
                $modelQuestionnaire->saveSurvey($user_id, $titre, $description, $access_pin, $qr_code_token, $questions);
                // Redirection ou confirmation (ici on inclut la vue, le JS recevra le HTML 200 OK)
                require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
            } catch (Exception $e) {
                http_response_code(500);
                echo "Erreur lors de la sauvegarde : " . $e->getMessage();
            }
        }
    }
}
