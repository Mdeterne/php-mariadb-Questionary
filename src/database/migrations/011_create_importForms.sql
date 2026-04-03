-- Active: 1758301938336@@localhost@3306@questionary
CREATE TABLE IF NOT EXISTS importedSurveys (
    id int AUTO_INCREMENT PRIMARY KEY,
    survey_id int,
    user_id int,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (survey_id) REFERENCES surveys (id)
);