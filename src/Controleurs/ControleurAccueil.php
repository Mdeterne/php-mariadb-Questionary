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
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Qcm' . DIRECTORY_SEPARATOR . 'questionnaireVueEleve.php');
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
        $donnees = json_decode(file_get_contents('php://input'), true);

        // Validation des données reçues
        if (!$donnees || !isset($donnees['survey_id']) || !isset($donnees['answers'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données invalides']);
            return;
        }

        $idQuestionnaire = $donnees['survey_id'];
        $reponses = $donnees['answers'];

        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Reponse.php';
        $modeleReponse = new Reponse();

        if ($modeleReponse->saveFullResponse($idQuestionnaire, $reponses)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
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
?>