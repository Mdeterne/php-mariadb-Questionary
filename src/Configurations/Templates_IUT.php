<?php

/**
 * Modèles de questionnaires pour l'IUT.
 */
return [
    'iut_annuel' => [
        'title' => "Évaluation Annuelle - Licence Pro / BUT",
        'description' => "Sondage de satisfaction globale sur l'année universitaire écoulée à l'IUT.",
        'questions' => [
            [
                'id' => 'q1',
                'type' => 'scale',
                'label' => "Globalement, êtes-vous satisfait de votre année ?",
                'is_required' => 1,
                'scale_min_label' => "Pas du tout",
                'scale_max_label' => "Totalement"
            ]
        ]
    ]
];
