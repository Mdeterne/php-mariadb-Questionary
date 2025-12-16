<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Configues".DIRECTORY_SEPARATOR."configue_CAS.php";
$_SESSION['mail'] = $infoSESSION['mail'];
$_SESSION['name'] = $infoSESSION['cn'];
$_SESSION['id'] = $infoSESSION['uid'];

// Controllers
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."homeControleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."tableauDeBordControlleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."creationQuestionnaireControleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."espaceAnalyseControleur.php";

$controleur = isset($_GET['c'])? $_GET['c'] : 'home';
$action = isset($_GET['a'])? $_GET['a'] : 'index';
$questionaire = isset($_GET['q'])? $_GET['q'] : '0';
$pin = isset($_GET['pin'])? $_GET['pin'] : '';


switch ($controleur){
  
  case 'home':
    $homeControleur = new homeControleur();
    switch ($action){
      case 'index':
        $homeControleur->index();
      break;

      case 'valider':
        $homeControleur->valider($pin);
      break;

      case 'saveReponse':
        $homeControleur->saveReponse();
      break;
    }
  break;

  case 'tableauDeBord':
  $tableauDeBordControlleur = new tableauDeBordControlleur();
  switch ($action){
          
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

    case 'conditionGenerales':
      $tableauDeBordControlleur->conditionGenerales();
    break;

    case 'confidentialite':
      $tableauDeBordControlleur->confidentialite();
    break;

    case 'parametres':
      $tableauDeBordControlleur->parametres();
    break;

    case 'saveSettings':
      $tableauDeBordControlleur->saveSettings();
    break;

    case 'utilisationCookie':
      $tableauDeBordControlleur->utilisationCookie();
    break;



  }
  break;

  case 'espaceAnalyse':
    $espaceAnalyseControleur = new espaceAnalyseControleur();
    switch ($action) {
      case 'index':
      default:
        $espaceAnalyseControleur->index();
      break;
    }
  break;

  case 'createur':
    $creationQuestionnaireControleur = new creationQuestionnaireControleur();

    switch ($action){
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
    $tableauDeBordControlleur = new tableauDeBordControlleur();
    $tableauDeBordControlleur->parametres();
  break;
}