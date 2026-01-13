<?php

session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Configs" . DIRECTORY_SEPARATOR . "ConfigCas.php";

$_SESSION['mail'] = $infoSESSION['mail'];
$_SESSION['name'] = $infoSESSION['cn'];
$_SESSION['id'] = $infoSESSION['uid'];


// Controllers
require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Controleurs" . DIRECTORY_SEPARATOR . "ControleurAccueil.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Controleurs" . DIRECTORY_SEPARATOR . "ControleurTableauDeBord.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Controleurs" . DIRECTORY_SEPARATOR . "ControleurCreation.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Controleurs" . DIRECTORY_SEPARATOR . "ControleurAnalyse.php";

$controleur = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';
$questionaire = isset($_GET['q']) ? $_GET['q'] : '0';
$pin = isset($_GET['pin']) ? $_GET['pin'] : '';


switch ($controleur) {

  case 'home':
    $homeControleur = new ControleurAccueil();
    switch ($action) {
      case 'index':
        $homeControleur->index();
        break;

      case 'valider':
        $homeControleur->valider($pin);
        break;

      case 'saveReponse':
        $homeControleur->saveReponse();
        break;

      case 'merci':
        $homeControleur->merci();
        break;

      case 'conditionGenerales':
        $homeControleur->conditionGenerales();
        break;

      case 'confidentialite':
        $homeControleur->confidentialite();
        break;

      case 'utilisationCookie':
        $homeControleur->utilisationCookie();
        break;
    }
    break;

  case 'tableauDeBord':
    $tableauDeBordControlleur = new ControleurTableauDeBord();
    $creationQuestionnaireControleur = new ControleurCreation();
    if ($_SESSION['role'] != 'enseignant') {
      $controleur = 'home';
      $action = null;
    }
    switch ($action) {

      case 'index':
        // var_dump($_SESSION);
        $tableauDeBordControlleur->index();
        break;

      case 'getMesQuestionnaires':
        $tableauDeBordControlleur->getMesQuestionnaires();
        break;

      case 'creerNouveau':
        $tableauDeBordControlleur->creerNouveau();
        break;

      case 'supprimer':
        $tableauDeBordControlleur->supprimer();
        break;

      case 'importer':
        $creationQuestionnaireControleur->import($pin);
        break;

      case 'parametres':
        $tableauDeBordControlleur->parametres();
        break;

      case 'saveSettings':
        $tableauDeBordControlleur->saveSettings();
        break;





    }
    break;

  case 'espaceAnalyse':
    $espaceAnalyseControleur = new ControleurAnalyse();
    if ($_SESSION['role'] != 'enseignant') {
      $controleur = 'home';
      $action = null;
      exit();
    }
    switch ($action) {
      case 'index':
      default:
        $espaceAnalyseControleur->index();
        break;
    }
    break;

  case 'createur':
    $creationQuestionnaireControleur = new ControleurCreation();
    if ($_SESSION['role'] != 'enseignant') {
      $controleur = 'home';
      $action = null;
    }

    switch ($action) {
      case 'nouveauFormulaire':
        $creationQuestionnaireControleur->nouveauFormulaire();
        break;

      case 'index':
        $creationQuestionnaireControleur->index();
        break;

      case 'save':
        $creationQuestionnaireControleur->save();
        break;

      case 'editer':
        $creationQuestionnaireControleur->editer();
        break;
    }
    break;

  case 'parametre':
    if ($_SESSION['role'] != 'enseignant') {
      $controleur = 'home';
      $action = null;
    }
    $tableauDeBordControlleur = new ControleurTableauDeBord();
    $tableauDeBordControlleur->parametres();
    break;
}