<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'Database.php';

class questionnaire{

    private $database = new Database();
    private $db = $database->getConnection();
    function exists($pin){
        //TODO verifier si le pin existe dans la base de données
    }

    function listerLesQuestions($pin){
        //TODO liste les questions d'un questionnaire grace au pin
    }

}


?>