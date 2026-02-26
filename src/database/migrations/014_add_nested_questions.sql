CREATE TABLE IF NOT EXISTS `questions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `survey_id` int(11) NOT NULL,
    `type` varchar(50) NOT NULL,
    `label` text NOT NULL,
    `order_index` int(11) NOT NULL DEFAULT 0,
    `is_required` tinyint(1) NOT NULL DEFAULT 0,
    `scale_min_label` varchar(255) DEFAULT NULL,
    `scale_max_label` varchar(255) DEFAULT NULL,
    `parent_question_id` int(11) DEFAULT NULL,
    `parent_option_label` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `survey_id` (`survey_id`),
    CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
    -- Parent question constraint. Cannot cascade delete if parent is deleted, handle in app.
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

ALTER TABLE questions
ADD COLUMN IF NOT EXISTS parent_question_id INT DEFAULT NULL AFTER scale_max_label,
ADD COLUMN IF NOT EXISTS parent_option_label VARCHAR(255) DEFAULT NULL AFTER parent_question_id,
ADD CONSTRAINT fk_parent_question FOREIGN KEY (parent_question_id) REFERENCES questions (id) ON DELETE SET NULL;