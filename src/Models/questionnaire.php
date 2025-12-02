<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'Database.php';

class questionnaire{

    private $database = new Database();
    private $db = $database->getConnection();
    function exists($pin){
        //verifier si le pin existe dans la base de données
    }

    function listerLesQuestions($pin){
        //liste les questions d'un questionnaire grace au pin
    }

}


?>