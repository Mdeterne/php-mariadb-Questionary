CREATE TABLE responses (
    id CHAR(36) PRIMARY KEY,
    survey_id CHAR(36) NOT NULL,
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    submitted_at DATETIME NULL,
    FOREIGN KEY (survey_id) REFERENCES surveys(id)
        ON DELETE CASCADE
);
