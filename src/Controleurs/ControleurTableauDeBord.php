<?php

class ControleurTableauDeBord
{
    /**
     * Affiche le tableau de bord avec la liste des questionnaires de l'utilisateur.
     */
    function index()
    {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php');
        $modeleQuestionnaire = new Questionnaire();

        // On suppose que l'ID utilisateur est en session
        $idUtilisateur = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
        $mesQuestionnaires = $modeleQuestionnaire->getSurveysByUserId($idUtilisateur);

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Notification.php');
        $modeleNotification = new Notification();
        $mesNotifications = $modeleNotification->recupererNotificationsUtilisateur($idUtilisateur);

        // Map fields for JS
        $notificationsJs = array_map(function ($n) {
            return [
                'id' => $n['id'],
                'message' => $n['message'],
                'read' => (bool) $n['is_read']
            ];
        }, $mesNotifications);

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'TableauDeBord' . DIRECTORY_SEPARATOR . 'dashboard.php');
    }

    /**
     * Affiche la page de notifications.
     */
    function notifications()
    {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Notification.php');
        $modeleNotification = new Notification();
        $idUtilisateur = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

        $mesNotifications = $modeleNotification->recupererNotificationsUtilisateur($idUtilisateur);

        // Map database fields to view expected fields if necessary (already matching: id, message, is_read -> read)
        $notificationsMappees = array_map(function ($n) {
            return [
                'id' => $n['id'],
                'message' => $n['message'],
                'read' => (bool) $n['is_read']
            ];
        }, $mesNotifications);

        $notifications = $notificationsMappees;

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'TableauDeBord' . DIRECTORY_SEPARATOR . 'notifications.php');
    }

    /**
     * Marque une notification comme lue via AJAX.
     */
    function marquerNotificationLue()
    {
        header('Content-Type: application/json');
        $donnees = json_decode(file_get_contents('php://input'), true);

        if (!$donnees || !isset($donnees['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
            exit;
        }

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Notification.php');
        $modeleNotification = new Notification();
        $idUtilisateur = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

        if ($modeleNotification->marquerCommeLu($donnees['id'], $idUtilisateur)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour']);
        }
        exit;
    }

    /**
     * Récupère la liste des questionnaires au format JSON.
     */
    function getMesQuestionnaires()
    {
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php');
        $modeleQuestionnaire = new Questionnaire();
        $idUtilisateur = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
        $mesQuestionnaires = $modeleQuestionnaire->getSurveysByUserId($idUtilisateur);

        header('Content-Type: application/json');
        echo json_encode($mesQuestionnaires);
        exit;
    }

    /**
     * Supprime un questionnaire.
     */
    function supprimer()
    {
        header('Content-Type: application/json');

        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $idUtilisateur = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

        if (!$id || !$idUtilisateur) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant ou utilisateur non connecté']);
            exit;
        }

        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php');
        $modeleQuestionnaire = new Questionnaire();

        if ($modeleQuestionnaire->deleteSurvey($id, $idUtilisateur)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Impossible de supprimer ou questionnaire introuvable']);
        }
        exit;
    }

    /**
     * Affiche la page de paramètres d'un questionnaire.
     */
    function parametres()
    {
        if (!isset($_GET['id'])) {
            header('Location: ?c=tableauDeBord');
            exit;
        }
        $id = $_GET['id'];
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php';
        $modele = new Questionnaire();
        $questionnaire = $modele->getSurveyById($id);

        if (!$questionnaire) {
            header('Location: ?c=tableauDeBord');
            exit;
        }

        // Variable utilisée par la vue : $survey
        $survey = $questionnaire;
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Parametres' . DIRECTORY_SEPARATOR . 'parametre.php';
    }

    /**
     * Enregistre les paramètres d'un questionnaire.
     */
    function saveSettings()
    {
        header('Content-Type: application/json');
        $donnees = json_decode(file_get_contents('php://input'), true);

        if (!$donnees || !isset($donnees['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
            exit;
        }

        $id = $donnees['id'];
        $idUtilisateur = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
        $accepterReponses = $donnees['acceptResponses']; // booléen

        $statut = $accepterReponses ? 'active' : 'closed';

        // Les autres paramètres sont stockés dans la colonne JSON
        $parametres = json_encode([
            'dateStart' => $donnees['dateStart'] ?? null,
            'dateEnd' => $donnees['dateEnd'] ?? null,
            'notifResponse' => $donnees['notifResponse'] ?? false,
            'notifLimit' => $donnees['notifLimit'] ?? false,
            'notifInvalid' => $donnees['notifInvalid'] ?? false
        ]);

        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php';
        $modele = new Questionnaire();

        if ($modele->updateSurveySettings($id, $idUtilisateur, $statut, $parametres)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la sauvegarde']);
        }
        exit;
    }
}