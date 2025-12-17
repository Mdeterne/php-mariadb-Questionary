<?php

session_start();

$_SESSION['role'] = '';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Configues".DIRECTORY_SEPARATOR."configue_CAS.php";
if (isset($infoSESSION)) {
    $_SESSION['mail'] = $infoSESSION['mail'];
    $_SESSION['name'] = $infoSESSION['cn'];
    $_SESSION['id'] = $infoSESSION['uid'];
} else {
    // Fallback for local development or when CAS is not active
    if (!isset($_SESSION['mail'])) $_SESSION['mail'] = 'dev@local.test';
    if (!isset($_SESSION['name'])) $_SESSION['name'] = 'Developpeur';
    if (!isset($_SESSION['id'])) $_SESSION['id'] = '1'; // Ensure this matches a valid user ID if needed
}

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

      case 'merci':
        $homeControleur->merci();
      break;
    }
  break;

  case 'tableauDeBord':
  $tableauDeBordControlleur = new tableauDeBordControlleur();
  if($_SESSION['role'] != 'enseignant'){
    $controleur = 'home';
    $action = null;
    require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
    exit();
  }
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
    if($_SESSION['role'] != 'enseignant'){
      $controleur = 'home';
      $action = null;
      require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
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
    $creationQuestionnaireControleur = new creationQuestionnaireControleur();
    if($_SESSION['role'] != 'enseignant'){
      $controleur = 'home';
      $action = null;
      require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
      exit();
    }

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
    if($_SESSION['role'] != 'enseignant'){
      $controleur = 'home';
      $action = null;
      require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
      exit();
    }
    $tableauDeBordControlleur = new tableauDeBordControlleur();
    $tableauDeBordControlleur->parametres();
  break;
}