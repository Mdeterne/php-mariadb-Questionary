CREATE TABLE surveys (
    id CHAR(36) PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    access_pin VARCHAR(6) UNIQUE,
    qr_code_token VARCHAR(64) UNIQUE,
    status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
    settings JSON,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
