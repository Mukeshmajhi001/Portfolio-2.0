-- ============================================================
-- Portfolio 2.0 — Database Setup Script
-- Run this ONCE to initialise the database
-- ============================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `portfolio2.0`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `portfolio2.0`;

-- ── Table: admin ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: projects ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `projects` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(150) NOT NULL,
  `description`  TEXT         NOT NULL,
  `technologies` VARCHAR(255) NOT NULL,
  `category`     ENUM('all','php','js','css','fullstack') NOT NULL DEFAULT 'all',
  `live_url`     VARCHAR(255) DEFAULT NULL,
  `github_url`   VARCHAR(255) DEFAULT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: project_images ─────────────────────────────────
CREATE TABLE IF NOT EXISTS `project_images` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id`   INT UNSIGNED NOT NULL,
  `image_path`   VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_project_images_project`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Table: messages ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `message`    TEXT         NOT NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Default Admin Account ─────────────────────────────────
-- Username : admin
-- Password : Admin@1234
-- Hash generated with: password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost'=>10])
INSERT INTO `admin` (`username`, `password`) VALUES (
  'admin',
  '$2y$10$cfAtLPLHUlHiDnCeSVG3pOaxQNpbumeYaW/RlMcuuFQdSZB3v1qZK'
) ON DUPLICATE KEY UPDATE `password` = '$2y$10$cfAtLPLHUlHiDnCeSVG3pOaxQNpbumeYaW/RlMcuuFQdSZB3v1qZK';

-- ── Sample Projects (optional — delete after testing) ─────
INSERT IGNORE INTO `projects` (`id`, `title`, `description`, `technologies`, `category`, `live_url`, `github_url`) VALUES
(1, 'E-Commerce Platform',
 'A fully featured online store with product management, shopping cart, Stripe payments, and a comprehensive admin dashboard. Handles inventory, orders, and customer accounts.',
 'PHP, MySQL, JavaScript, CSS3, Stripe API', 'fullstack',
 'https://demo.example.com', 'https://github.com/example/ecommerce'),

(2, 'Task Manager Pro',
 'A Kanban-style project management tool with drag-and-drop boards, real-time collaboration, task assignments, due dates, and priority labels. Built as a SPA.',
 'JavaScript, Vue.js, PHP, MySQL, WebSocket', 'js',
 'https://taskpro.example.com', 'https://github.com/example/taskpro'),

(3, 'Portfolio Generator',
 'A drag-and-drop portfolio builder with 12 customisable themes, one-click PDF export, custom domain support, and an analytics dashboard.',
 'PHP, MySQL, CSS3, JavaScript', 'php',
 'https://portfoliobuilder.example.com', 'https://github.com/example/portfolio-gen'),

(4, 'CSS Animation Library',
 'A lightweight, zero-dependency CSS animation library with 80+ ready-to-use animations. Features a live playground, customisation tools, and npm/CDN distribution.',
 'CSS3, JavaScript, Webpack', 'css',
 'https://animlib.example.com', 'https://github.com/example/animlib'),

(5, 'Blog CMS',
 'A headless CMS built with PHP and a React front-end. Supports markdown, code highlighting, categories, tags, RSS feeds, and an SEO toolkit.',
 'PHP, React, MySQL, REST API', 'fullstack',
 'https://blogcms.example.com', 'https://github.com/example/blogcms'),

(6, 'Real-Time Chat App',
 'A real-time messaging application with rooms, direct messages, file sharing, emoji reactions, read receipts, and push notifications.',
 'Node.js, Socket.io, JavaScript, CSS3', 'js',
 'https://chat.example.com', 'https://github.com/example/chatapp');

-- ── Re-generate admin password (run this in PHP CLI if needed) ─
-- php -r "echo password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost'=>12]);"
-- Then UPDATE admin SET password='<output>' WHERE username='admin';
