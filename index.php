<?php

session_start();

//require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Configues".DIRECTORY_SEPARATOR."configue_CAS.php";
$questionaire = isset($_GET['q'])? $_GET['q'] : '0';

// Controllers
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."homeControleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."tableauDeBordControlleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."creationQuestionnaireControleur.php";

$controleur = isset($_GET['c'])? $_GET['c'] : 'home';
$action = isset($_GET['a'])? $_GET['a'] : 'index';


switch ($controleur){
  
  case 'home':
    $homeControleur = new homeControleur();
    switch ($action){
      case 'index':
        $homeControleur->index();
      break;

      case 'valider':
        $homeControleur->valider();
      break;
    }
  break;

  case 'tableauDeBord':
  $tableauDeBordControlleur = new tableauDeBordControlleur();
  switch ($action){
          
    case 'index':
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

    case 'utilisationCookie':
      $tableauDeBordControlleur->utilisationCookie();
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
    }
  break;

  case 'parametre':
    $tableauDeBordControlleur = new tableauDeBordControlleur();
    $tableauDeBordControlleur->parametres();
  break;
}