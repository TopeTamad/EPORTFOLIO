-- Database: eportfolio_chrisystematixx

CREATE DATABASE IF NOT EXISTS eportfolio_chrisystematixx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eportfolio_chrisystematixx;

CREATE TABLE IF NOT EXISTS profile (
  id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(150) NOT NULL,
  headline VARCHAR(255) NOT NULL,
  about TEXT NOT NULL,
  avatar VARCHAR(255) NULL,
  email VARCHAR(150) NULL,
  location VARCHAR(150) NULL,
  facebook_url VARCHAR(255) NULL,
  instagram_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS skills (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  level TINYINT NOT NULL CHECK (level BETWEEN 0 AND 100),
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS projects (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  tech_stack VARCHAR(255) NULL,
  project_url VARCHAR(255) NULL,
  image_url VARCHAR(255) NULL,
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS certificates (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  issuer VARCHAR(200) NULL,
  issue_date DATE NULL,
  credential_url VARCHAR(255) NULL,
  image_url VARCHAR(255) NULL,
  sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO profile (full_name, headline, about, avatar, email, location) VALUES
('Christopher Madeja Panoy', 'Aspiring System Developer | PHP Enthusiast', 'Driven, disciplined, and detail-oriented. I design and build clean, efficient, and reliable systems. Curious mind. Purpose-led. Passionate about transforming ideas into scalable web solutions.', 'assets/img/profile.jpg', 'christopher@example.com', 'Philippines');

INSERT INTO skills (name, level, sort_order) VALUES
('HTML', 80, 1),
('C++', 50, 2),
('PHP', 80, 3),
('Python', 50, 4);

INSERT INTO projects (title, description, tech_stack, project_url, image_url, sort_order) VALUES
('Student E-Portfolio', 'A modern, database-backed portfolio showcasing academic projects, certifications, and skills.', 'PHP, MySQL, CSS, JS', '#', 'https://picsum.photos/seed/portfolio/800/600', 1),
('Task Tracker', 'Lightweight task management app with CRUD features and clean UI.', 'PHP, SQLite/MySQL, Vanilla JS', '#', 'https://picsum.photos/seed/tasks/800/600', 2);

INSERT INTO certificates (title, issuer, issue_date, credential_url, image_url, sort_order) VALUES
('Web Development Fundamentals', 'Open Education', '2023-07-15', '#', 'https://picsum.photos/seed/cert1/800/600', 1),
('C++ Programming Basics', 'Tech Academy', '2024-02-10', '#', 'https://picsum.photos/seed/cert2/800/600', 2);
