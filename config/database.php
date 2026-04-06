<?php
/**
 * Database Configuration
 * Portfolio 2.0 - PDO Connection with Security
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio2.0');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                die(json_encode(['error' => 'Database connection failed.']));
            }
        }
        return self::$instance;
    }
}

/**
 * SQL to create database and tables — run once.
 * 
 * CREATE DATABASE IF NOT EXISTS `portfolio2.0` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 * USE `portfolio2.0`;
 *
 * CREATE TABLE admin (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   username VARCHAR(50) NOT NULL UNIQUE,
 *   password VARCHAR(255) NOT NULL,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 *
 * CREATE TABLE projects (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   title VARCHAR(150) NOT NULL,
 *   description TEXT NOT NULL,
 *   technologies VARCHAR(255),
 *   category VARCHAR(50) NOT NULL DEFAULT 'all',
 *   live_url VARCHAR(255),
 *   github_url VARCHAR(255),
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 *
 * CREATE TABLE project_images (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   project_id INT NOT NULL,
 *   image_path VARCHAR(255) NOT NULL,
 *   FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
 * );
 *
 * CREATE TABLE messages (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   name VARCHAR(100) NOT NULL,
 *   email VARCHAR(150) NOT NULL,
 *   message TEXT NOT NULL,
 *   is_read TINYINT(1) DEFAULT 0,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 *
 * -- Default admin: username=admin, password=Admin@1234
 * INSERT INTO admin (username, password) VALUES
 * ('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
 */
