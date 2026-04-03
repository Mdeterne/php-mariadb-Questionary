CREATE TABLE IF NOT EXISTS survey_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    tag VARCHAR(50) NOT NULL,
    FOREIGN KEY (survey_id) REFERENCES surveys (id) ON DELETE CASCADE,
    UNIQUE KEY unique_survey_tag (survey_id, tag)
);
