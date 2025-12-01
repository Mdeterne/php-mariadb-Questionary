CREATE TABLE question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    label VARCHAR(255) NOT NULL,
    order_index INT DEFAULT 0,
    is_open_ended BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(id)
        ON DELETE CASCADE
);
