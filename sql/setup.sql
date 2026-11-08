SET default_storage_engine = 'InnoDB';
SET NAMES utf8mb4;

DROP VIEW IF EXISTS latest_comments;
DROP VIEW IF EXISTS post_overview;
DROP TABLE IF EXISTS post_history;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS content_pages;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users
(
    user_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(254) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('member', 'admin') NOT NULL DEFAULT 'member',
    is_banned BOOLEAN NOT NULL DEFAULT FALSE,
    bio TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories
(
    category_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts
(
    post_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    visibility ENUM('visible', 'hidden') NOT NULL DEFAULT 'visible',
    is_hot BOOLEAN NOT NULL DEFAULT FALSE,
    is_sticky BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (category_id) REFERENCES categories (category_id)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE likes
(
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT PK_likes PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts (post_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comments
(
    comment_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (post_id) REFERENCES posts (post_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_settings
(
    setting_key VARCHAR(80) NOT NULL PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_by INT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users (user_id) ON DELETE SET NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE content_pages
(
    page_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    slug VARCHAR(80) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,
    updated_by INT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users (user_id) ON DELETE SET NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (name) VALUES ('General');

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS post_history
(
    history_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    post_id INT NOT NULL,
    event_type VARCHAR(20) NOT NULL,
    old_title VARCHAR(150),
    new_title VARCHAR(150) NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts (post_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX IF NOT EXISTS idx_posts_visibility_date ON posts (visibility, created_at, post_id);
CREATE INDEX IF NOT EXISTS idx_comments_date ON comments (created_at, comment_id);

CREATE OR REPLACE SQL SECURITY INVOKER VIEW post_overview AS
SELECT p.*, u.username, u.is_banned, c.name AS category_name,
    (SELECT COUNT(*) FROM likes l JOIN users lu ON l.user_id = lu.user_id WHERE l.post_id = p.post_id AND lu.is_banned = 0) AS like_count,
    (SELECT COUNT(*) FROM comments cm JOIN users cu ON cm.user_id = cu.user_id WHERE cm.post_id = p.post_id AND cu.is_banned = 0) AS comment_count
FROM posts p
JOIN users u ON p.user_id = u.user_id
JOIN categories c ON p.category_id = c.category_id;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW latest_comments AS
SELECT cm.comment_id, cm.post_id, cm.user_id, cm.body, cm.created_at, u.username, p.title
FROM comments cm
JOIN users u ON cm.user_id = u.user_id
JOIN posts p ON cm.post_id = p.post_id
JOIN users author ON p.user_id = author.user_id
WHERE p.visibility = 'visible' AND u.is_banned = 0 AND author.is_banned = 0;

DROP TRIGGER IF EXISTS post_created;
DROP TRIGGER IF EXISTS post_updated;

CREATE TRIGGER post_created AFTER INSERT ON posts
FOR EACH ROW
INSERT INTO post_history (post_id, event_type, new_title)
VALUES (NEW.post_id, 'created', NEW.title);

CREATE TRIGGER post_updated AFTER UPDATE ON posts
FOR EACH ROW
INSERT INTO post_history (post_id, event_type, old_title, new_title)
VALUES (NEW.post_id, 'updated', OLD.title, NEW.title);

INSERT INTO content_pages (slug, title, body, is_published)
SELECT 'about', 'About', 'SetupForge is a place to share desk and gaming setups.', 1
WHERE NOT EXISTS (SELECT 1 FROM content_pages WHERE slug = 'about');
INSERT INTO content_pages (slug, title, body, is_published)
SELECT 'rules', 'Rules', 'Be respectful. Only upload pictures you have permission to share.', 1
WHERE NOT EXISTS (SELECT 1 FROM content_pages WHERE slug = 'rules');
INSERT INTO content_pages (slug, title, body, is_published)
SELECT 'contact', 'Contact', 'Contact information will be added by the owner.', 1
WHERE NOT EXISTS (SELECT 1 FROM content_pages WHERE slug = 'contact');

INSERT INTO site_settings (setting_key, setting_value)
SELECT 'site_name', 'SetupForge' WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'site_name');
INSERT INTO site_settings (setting_key, setting_value)
SELECT 'custom_style', '0' WHERE NOT EXISTS (SELECT 1 FROM site_settings WHERE setting_key = 'custom_style');

INSERT INTO users (username, email, password_hash, bio) VALUES
('demo_alex', 'alex@example.com', '!LOGIN_DISABLED', 'I like simple desk setups.'),
('demo_sam', 'sam@example.com', '!LOGIN_DISABLED', 'Gaming and building PCs.');

INSERT INTO posts (user_id, category_id, title, description, image_path, is_hot, is_sticky) VALUES
(1, 1, 'Study desk', 'A **simple** workspace for coding.', 'assets/sample-1.svg', 1, 1),
(2, 1, 'Gaming desk', 'Trying a different monitor layout.', 'assets/sample-2.svg', 1, 0),
(1, 1, 'Small workspace', 'Keeping the desk clear.', 'assets/sample-3.svg', 0, 0);

INSERT INTO comments (user_id, post_id, body) VALUES
(2, 1, 'Nice setup!'), (1, 2, 'I like the monitor position.');
INSERT INTO likes (user_id, post_id) VALUES (2, 1), (1, 2);
