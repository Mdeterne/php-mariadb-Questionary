CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id int NOT NULL,
    question_id INT NOT NULL,
    text_value TEXT NULL,
    FOREIGN KEY (response_id) REFERENCES responses(id)
        ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
        ON DELETE CASCADE
);
