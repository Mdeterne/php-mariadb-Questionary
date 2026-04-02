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
    ],
    'iut_module' => [
        'title' => "Évaluation d'un Module d'Enseignement",
        'description' => "Ressenti sur la pédagogie, le rythme et le contenu d'une matière spécifique.",
        'questions' => [
            [
                'id' => 'm1',
                'type' => 'scale',
                'label' => "Intérêt général du sujet traité dans ce module",
                'is_required' => 1,
                'scale_min_label' => "Faible",
                'scale_max_label' => "Passionnant"
            ],
            [
                'id' => 'm2',
                'type' => 'scale',
                'label' => "Clarté des explications fournies par l'enseignant",
                'is_required' => 1,
                'scale_min_label' => "Confus",
                'scale_max_label' => "Très clair"
            ],
            [
                'id' => 'm3',
                'type' => 'single_choice',
                'label' => "Que pensez-vous de l'équilibre entre la théorie (CM) et la pratique (TP) ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Équilibre parfait", 'is_open_ended' => 0],
                    ['label' => "Trop de théorie / Pas assez de TP", 'is_open_ended' => 0],
                    ['label' => "Trop de TP / Pas assez de cours", 'is_open_ended' => 0]
                ]
            ],
            [
                'id' => 'm4',
                'type' => 'long_text',
                'label' => "Un point fort et un point faible à souligner sur ce module ?",
                'is_required' => 0
            ]
        ]
    ],
    'iut_vie' => [
        'title' => "Sondage Vie Étudiante & Services IUT",
        'description' => "Qualité de l'accueil, de l'infrastructure et de l'ambiance au sein de l'école.",
        'questions' => [
            [
                'id' => 'v1',
                'type' => 'scale',
                'label' => "Comment évaluez-vous votre intégration au sein de votre promotion ?",
                'is_required' => 1,
                'scale_min_label' => "Isolé(e)",
                'scale_max_label' => "Épanoui(e)"
            ],
            [
                'id' => 'v2',
                'type' => 'multiple_choice',
                'label' => "Quels services de l'IUT utilisez-vous régulièrement ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Bibliothèque Universitaire (BU)", 'is_open_ended' => 0],
                    ['label' => "Restaurant Universitaire / Cafétéria", 'is_open_ended' => 0],
                    ['label' => "Salles informatiques en libre accès", 'is_open_ended' => 0],
                    ['label' => "Tutorat / Soutien", 'is_open_ended' => 0],
                    ['label' => "Bureau des Étudiants (BDE)", 'is_open_ended' => 0]
                ]
            ],
            [
                'id' => 'v3',
                'type' => 'single_choice',
                'label' => "Participez-vous activement aux événements organisés par l'IUT ?",
                'is_required' => 0,
                'options' => [
                    ['label' => "Oui, très souvent", 'is_open_ended' => 0],
                    ['label' => "Oui, de temps en temps", 'is_open_ended' => 0],
                    ['label' => "Non, jamais", 'is_open_ended' => 0]
                ]
            ],
            [
                'id' => 'v4',
                'type' => 'long_text',
                'label' => "Si vous pouviez changer une seule chose à l'IUT pour le bien-être des étudiants, ce serait quoi ?",
                'is_required' => 0
            ]
        ]
    ],
    'iut_competences' => [
        'title' => "Bilan d'Auto-Évaluation de Compétences",
        'description' => "Analyse des compétences acquises pour alimenter le Portfolio BUT.",
        'questions' => [
            [
                'id' => 'c1',
                'type' => 'scale',
                'label' => "Quelle est votre maîtrise globale des outils techniques vus cette année ?",
                'is_required' => 1,
                'scale_min_label' => "Débutant",
                'scale_max_label' => "Expert"
            ],
            [
                'id' => 'c2',
                'type' => 'multiple_choice',
                'label' => "Quelles compétences transversales avez-vous le plus développées ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Travail en équipe / Collaboration", 'is_open_ended' => 0],
                    ['label' => "Communication orale et écrite", 'is_open_ended' => 0],
                    ['label' => "Gestion du temps et autonomie", 'is_open_ended' => 0],
                    ['label' => "Esprit critique et analyse", 'is_open_ended' => 0],
                    ['label' => "Adaptabilité et stress", 'is_open_ended' => 0]
                ]
            ],
            [
                'id' => 'c3',
                'type' => 'long_text',
                'label' => "Décrivez brièvement la réalisation concrète dont vous êtes le plus fier cette année.",
                'is_required' => 1
            ],
            [
                'id' => 'c4',
                'type' => 'single_choice',
                'label' => "Vous sentez-vous prêt pour affronter la suite (stage, monde pro, année suivante) ?",
                'is_required' => 1,
                'options' => [
                    ['label' => "Oui, totalement serein", 'is_open_ended' => 0],
                    ['label' => "Globalement oui, avec quelques doutes", 'is_open_ended' => 0],
                    ['label' => "Non, pas encore tout à fait", 'is_open_ended' => 0]
                ]
            ]
        ]
    ]
];
