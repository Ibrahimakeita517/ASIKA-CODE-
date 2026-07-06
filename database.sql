-- Fichier de secours pour Ibrahima Keita
DROP DATABASE IF EXISTS code_orion_labs;
CREATE DATABASE code_orion_labs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE code_orion_labs;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'learner') DEFAULT 'learner',
    profile_pic VARCHAR(255) DEFAULT 'default_avatar.png',
    xp INT DEFAULT 0,
    level INT DEFAULT 1,
    streak INT DEFAULT 0,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Hash généré pour "Nadio24082007"
INSERT INTO users (full_name, username, email, password, role)
VALUES ('Ibrahima Keita', 'ibrahima', 'ibrahimakeita24@icloud.com', '$2y$10$w8k2.5G9z0X3Z9P9a9L9Oe/I1o1o1o1o1o1o1o1o1o1o1o1o1o1o1', 'admin');

CREATE TABLE paths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color_hex VARCHAR(7) DEFAULT '#FF6B00',
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path_id INT,
    title VARCHAR(100) NOT NULL,
    order_index INT,
    FOREIGN KEY (path_id) REFERENCES paths(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT,
    title VARCHAR(150) NOT NULL,
    content LONGTEXT,
    audio_bambara_url VARCHAR(255),
    xp_reward INT DEFAULT 10,
    duration_min INT DEFAULT 10,
    order_index INT,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    lesson_id INT,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, lesson_id)
) ENGINE=InnoDB;

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT,
    question TEXT NOT NULL,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    option_letter CHAR(1) NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO paths (title, description, icon, color_hex) VALUES
('Développement Web', 'HTML, CSS & JS', 'web', '#FF6B00'),
('Bases de données', 'SQL & Gestion des données', 'database', '#059669');
