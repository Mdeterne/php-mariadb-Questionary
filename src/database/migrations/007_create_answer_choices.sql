CREATE TABLE answer_choices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    answer_id INT NOT NULL,
    option_id INT NOT NULL,
    FOREIGN KEY (answer_id) REFERENCES answers(id)
        ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES question_options(id)
        ON DELETE CASCADE
);
