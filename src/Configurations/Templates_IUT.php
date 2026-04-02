<?php

/**
 * Bibliothèque des modèles de questionnaires pour l'IUT.
 * Ces modèles permettent aux enseignants de créer rapidement des sondages types.
 * Format compatible avec l'importation directe dans l'éditeur Vue.js.
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
            ],
            [
                'id' => 'q2',
                'type' => 'single_choice',
                'label' => "Quel aspect de la formation avez-vous le plus apprécié ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Les cours magistraux", 'is_open_ended' => 0],
                    ['label' => "Les travaux pratiques (TP)", 'is_open_ended' => 0],
                    ['label' => "Les projets de groupe", 'is_open_ended' => 0],
                    ['label' => "Les stages / alternance", 'is_open_ended' => 0],
                    ['label' => "Autre", 'is_open_ended' => 1]
                ]
            ],
            [
                'id' => 'q3',
                'type' => 'long_text',
                'label' => "Quelles sont vos suggestions pour améliorer le département (locaux, matériels, organisation) ?",
                'is_required' => 0
            ],
            [
                'id' => 'q4',
                'type' => 'scale',
                'label' => "Évaluez la qualité de l'accompagnement pédagogique des enseignants.",
                'is_required' => 0,
                'scale_min_label' => "Médiocre",
                'scale_max_label' => "Excellente"
            ]
        ]
    ],
    'iut_stage' => [
        'title' => "Bilan d'Expérience Professionnelle (Stage/Alternance)",
        'description' => "Retour sur les missions effectuées en entreprise et l'insertion professionnelle.",
        'questions' => [
            [
                'id' => 's1',
                'type' => 'short_text',
                'label' => "Nom de l'entreprise d'accueil",
                'is_required' => 1
            ],
            [
                'id' => 's2',
                'type' => 'single_choice',
                'label' => "Considérez-vous vos missions comme étant en adéquation avec votre formation ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Tout à fait", 'is_open_ended' => 0],
                    ['label' => "Plutôt oui", 'is_open_ended' => 0],
                    ['label' => "Moyennement", 'is_open_ended' => 0],
                    ['label' => "Pas du tout", 'is_open_ended' => 0]
                ]
            ],
            [
                'id' => 's3',
                'type' => 'scale',
                'label' => "Comment évaluez-vous l'encadrement par votre tuteur en entreprise ?",
                'is_required' => 1,
                'scale_min_label' => "Insuffisant",
                'scale_max_label' => "Parfait"
            ],
            [
                'id' => 's4',
                'type' => 'long_text',
                'label' => "Quelles technologies ou compétences clés avez-vous développées ?",
                'is_required' => 0
            ]
        ]
    ],
    'iut_projet' => [
        'title' => "Revue de Projet Tuteuré",
        'description' => "Évaluation du travail en équipe, de la gestion de projet et des livrables techniques.",
        'questions' => [
            [
                'id' => 'p1',
                'type' => 'short_text',
                'label' => "Titre du projet réalisé",
                'is_required' => 1
            ],
            [
                'id' => 'p2',
                'type' => 'scale',
                'label' => "Qualité de la collaboration au sein de votre équipe de projet",
                'is_required' => 1,
                'scale_min_label' => "Difficile",
                'scale_max_label' => "Harmonieuse"
            ],
            [
                'id' => 'p3',
                'type' => 'multiple_choice',
                'label' => "Quels ont été les principaux défis techniques ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Développement Front-end", 'is_open_ended' => 0],
                    ['label' => "Logique Back-end / Base de données", 'is_open_ended' => 0],
                    ['label' => "Gestion du déploiement", 'is_open_ended' => 0],
                    ['label' => "Analyse et conception (UML, etc.)", 'is_open_ended' => 0]
                ]
            ],
            [
                'id' => 'p4',
                'type' => 'long_text',
                'label' => "Résumé des fonctionnalités majeures du livrable final",
                'is_required' => 1
            ]
        ]
    ]
];
