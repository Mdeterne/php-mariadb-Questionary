<?php

session_start();

//require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Configues".DIRECTORY_SEPARATOR."configue_CAS.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."homeControleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."connexionControleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."creerUnCompteControleur.php";
require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."createurControleur.php";

require_once __dir__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Controleurs".DIRECTORY_SEPARATOR."espacePersoControleur.php";


$controleur = isset($_GET['c'])? $_GET['c'] : 'home';
$action = isset($_GET['a'])? $_GET['a'] : 'index';
$questionaire = isset($_GET['q'])? $_GET['q'] : '0';

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

  //case 'connexion':
  //  $connexionControleur = new connexionControleur();
  //  switch ($action){
  //    case 'index':
  //      $connexionControleur->index();
  //    break;

  //    case 'connexion':
  //      $connexionControleur->connexion();
  //    break;
  //  }break;
  
  // case 'creerUnCompte':
  //   $creerUnCompteControleur = new creerUnCompteControleur();
  //   switch ($action){
  //     case 'index':
  //       $creerUnCompteControleur->index();

  //     case 'creerUnCompte':
  //       $creerUnCompteControleur->creerCompte();
  //     break;
  //   }break;

  case 'espacePerso':
  $espacePersoControleur = new espacePersoControleur();
  switch ($action){
          
    case 'index':
      $espacePersoControleur->index();
    break;
          
    case 'getMesQuestionnaires':
      $espacePersoControleur->getMesQuestionnaires();
    break;

    case 'creerNouveau':
      $espacePersoControleur->creerNouveau();
    break;
        
    case 'supprimer':
      $espacePersoControleur->supprimer();
    break;

  case 'questionaire':
    $questionaireControleur = new questionnaireControleur();
    switch ($action){
      case 'index':
        $questionaireControleur->index($q);
      break;
    }
  }break;
}