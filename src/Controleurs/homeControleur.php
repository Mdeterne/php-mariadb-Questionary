<?php

class homeControleur {
    function valider(){

        $pin = $_POST['pin'];

        echo $pin;
        //if ($pin = pinDansLaBase){  Regarde si le pin entré existe dans la base de donné
        //  récupère la page associé au pin et l'affiche
        //}else{
        //  affiche un erreur : "Le code pin n'a pas été correcctement entré"
        //}

        }
 
    function index(){
        //lien vers la vue
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
    }
}
