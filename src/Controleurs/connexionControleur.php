<?php

class connexionControleur{

    function index(){
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'connexion'.DIRECTORY_SEPARATOR.'connexion.php');
    }

    function connexion(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        //require_once __dir__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'connexion'.DIRECTORY_SEPARATOR.'connexion.php';

    }
}