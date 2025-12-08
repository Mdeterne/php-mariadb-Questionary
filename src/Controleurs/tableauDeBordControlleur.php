<?php

class tableauDeBordControlleur
{

    function index()
    {
        require_once(__DIR__ . '/../Views/espace_perso/dashboard.php');
    }

    function getMesQuestionnaires()
    {
        // //TODO (Back-End): Renvoyer les vraies données
        header('Content-Type: application/json');
        echo json_encode([]); // Renvoie un tableau vide
        exit;
    }

    function creerNouveau()
    {

        // TODO (Back-End): Créer en BDD et renvoyer le vrai ID
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success_mock',
            'nouveau_id' => 999
        ]);
        exit;
    }

    function supprimer()
    {
        // TODO (Back-End): Supprimer en BDD
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success_mock']);
        exit;
    }

    function conditionGenerales()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'legal' . DIRECTORY_SEPARATOR . 'conditionGenerales.php';
    }

    function confidentialite()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'legal' . DIRECTORY_SEPARATOR . 'confidentialite.php';
    }

    function parametres()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Parametres' . DIRECTORY_SEPARATOR . 'parametre.php';
    }
    function utilisationCookie()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'legal' . DIRECTORY_SEPARATOR . 'utilisationCookie.php';
    }
}