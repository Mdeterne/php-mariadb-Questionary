<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'Database.php';

class questionnaire{

    private $conn;

    function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    function exists($pin){
        //TODO verifier si le pin existe dans la base de données
    }

    function listerLesQuestions($pin){
        //TODO liste les questions d'un questionnaire grace au pin
    }

}


?>