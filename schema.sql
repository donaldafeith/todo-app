CREATE DATABASE todo_app;

USE todo_app;

CREATE TABLE todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    notes TEXT,
    date_received DATE,
    date_to_be_delivered DATE,
    status ENUM('pending', 'complete') DEFAULT 'pending'
);

CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    todo_id INT,
    file_path VARCHAR(255),
    description TEXT,
    FOREIGN KEY (todo_id) REFERENCES todos(id) ON DELETE CASCADE
);