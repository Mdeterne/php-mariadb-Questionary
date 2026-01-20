<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Utilisateur.php';
class ControleurAccueil
{
    /**
     * Valide un code PIN et redirige l'utilisateur vers le questionnaire ou l'interface appropriée.
     *
     * @param string $pin Le code PIN fourni.
     */
    function valider($pin)
    {
        $modeleQuestionnaire = new Questionnaire();
        
        // Vérifie si le questionnaire existe et est actuellement ouvert
        if ($modeleQuestionnaire->existsAndOpen($pin)) {
            $questionQuestionnaire = $modeleQuestionnaire->listerLesQuestions($pin);
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'qcm' . DIRECTORY_SEPARATOR . 'questionnaireVueEleve.php');
        } else {
            // Affiche la page d'erreur si le questionnaire n'est pas trouvé
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'confirmation' . DIRECTORY_SEPARATOR . 'questionnaireNonTrouve.php');
        }
    }

    /**
     * Affiche la page d'accueil ou le tableau de bord selon le rôle de l'utilisateur.
     */
    function index()
    {
        if ($_SESSION['role'] == 'etudiant') {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Accueil' . DIRECTORY_SEPARATOR . 'home.php');
        }
        if ($_SESSION['role'] == 'enseignant') {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'TableauDeBord' . DIRECTORY_SEPARATOR . 'dashboard.php');
        }
    }

    /**
     * Enregistre les réponses d'un utilisateur à un questionnaire.
     */
    function saveReponse()
    {
        header('Content-Type: application/json');
        
        // DEBUG: Logging request
        $logFile = __DIR__ . '/../../debug_submission.log';
        $input = file_get_contents('php://input');
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Received: " . $input . "\n", FILE_APPEND);

        try {
            $donnees = json_decode($input, true);

            // Validation des données reçues
            if (!$donnees || !isset($donnees['survey_id']) || !isset($donnees['answers'])) {
                file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Error: Invalid Data\n", FILE_APPEND);
                throw new Exception('Données invalides ou incomplètes.');
            }

            $idQuestionnaire = $donnees['survey_id'];
            $reponses = $donnees['answers'];

            require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Reponse.php';
            $modeleReponse = new Reponse();

            $modeleReponse->saveFullResponse($idQuestionnaire, $reponses);
            
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Success: Saved\n", FILE_APPEND);
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Affiche la page de remerciement.
     */
    function merci()
    {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Confirmation' . DIRECTORY_SEPARATOR . 'merci.php');
    }

    /**
     * Affiche la page des conditions générales.
     */
    function conditionGenerales()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Legal' . DIRECTORY_SEPARATOR . 'conditionGenerales.php';
    }

    /**
     * Affiche la politique de confidentialité.
     */
    function confidentialite()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Legal' . DIRECTORY_SEPARATOR . 'confidentialite.php';
    }

    /**
     * Affiche la page sur l'utilisation des cookies.
     */
    function utilisationCookie()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Legal' . DIRECTORY_SEPARATOR . 'utilisationCookie.php';
    }
}
