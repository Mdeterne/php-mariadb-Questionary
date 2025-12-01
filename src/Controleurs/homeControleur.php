<?php

class homeControleur {
    function valider(){

        $pin = $_POST['pin'];

        $home = new home();
        if($home->exists($pin)){
            $questionnaire = $home->trouverquestionnaire($pin);
        }

    }
 
    function index(){
        //lien vers la vue
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
    }
}
