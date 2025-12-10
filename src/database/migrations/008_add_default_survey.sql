-- Création d'un questionnaire par défaut avec des questions de test

INSERT INTO surveys (id, user_id, title, description, access_pin, status, created_at) 
VALUES (1, 1, 'Questionnaire Par Défaut', 'Un questionnaire de démonstration avec des questions de test', '123456', 'active', NOW());

-- Questions pour le questionnaire par défaut
INSERT INTO questions (survey_id, type, label, order_index, is_required) 
VALUES 
(1, 'text', 'Quel est votre nom ?', 1, true),
(1, 'single_choice', 'Êtes-vous satisfait du service ?', 2, true),
(1, 'multiple_choice', 'Quels sujets vous intéressent ?', 3, false);

-- Options pour la question à choix unique (question 2)
INSERT INTO question_options (question_id, label, order_index, is_open_ended)
SELECT id, label, order_index, is_open_ended FROM (
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 2) as id,
        'Très satisfait' as label,
        1 as order_index,
        false as is_open_ended
    UNION ALL
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 2),
        'Satisfait',
        2,
        false
    UNION ALL
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 2),
        'Peu satisfait',
        3,
        false
    UNION ALL
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 2),
        'Pas du tout satisfait',
        4,
        false
) AS options
WHERE id IS NOT NULL;

-- Options pour la question à choix multiples (question 3)
INSERT INTO question_options (question_id, label, order_index, is_open_ended)
SELECT id, label, order_index, is_open_ended FROM (
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 3) as id,
        'Technologie' as label,
        1 as order_index,
        false as is_open_ended
    UNION ALL
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 3),
        'Design',
        2,
        false
    UNION ALL
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 3),
        'Éducation',
        3,
        false
    UNION ALL
    SELECT 
        (SELECT id FROM questions WHERE survey_id = 1 AND order_index = 3),
        'Autre',
        4,
        true
) AS options
WHERE id IS NOT NULL;
