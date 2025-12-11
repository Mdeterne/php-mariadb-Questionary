CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id int NOT NULL,
    type ENUM('short_text', 'long_text', 'scale', 'single_choice', 'multiple_choice') NOT NULL,
    label TEXT NOT NULL,
    order_index INT DEFAULT 0,
    is_required BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (survey_id) REFERENCES surveys(id)
        ON DELETE CASCADE
);
