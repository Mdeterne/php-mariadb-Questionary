CREATE TABLE responses (
    id int AUTO_INCREMENT PRIMARY KEY,
    survey_id int NOT NULL,
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME NULL,
    FOREIGN KEY (survey_id) REFERENCES surveys(id)
        ON DELETE CASCADE
);
