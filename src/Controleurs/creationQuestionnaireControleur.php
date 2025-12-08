<?php

class creationQuestionnaireControleur
{
    function nouveauFormulaire()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'creation_questionnaire' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
    }

    function index()
    {
        $this->nouveauFormulaire();
    }
}
