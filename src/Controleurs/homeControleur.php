<?php

class homeControleur {
    function valider(){

        $pin = $_POST['pin'];

        $modelQuestionnaire = new questionnaire();
        if($modelQuestionnaire->exists($pin)){
            $questionQuestionnaire = $modelQuestionnaire->listerLesQuestions($pin);
        }else{
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
        }

    }
 
    function index(){
        //lien vers la vue
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'confirmation'.DIRECTORY_SEPARATOR.'questionnaireNonTrouve.php');
    }
}
