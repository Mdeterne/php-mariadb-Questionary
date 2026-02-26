CREATE TABLE responses (
    id int AUTO_INCREMENT PRIMARY KEY,
    survey_id int NOT NULL,
    user_id VARCHAR(255),
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME NULL,
    FOREIGN KEY (survey_id) REFERENCES surveys (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
);