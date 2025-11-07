-- Schema blog_db

-- This command attempts to create a new database named 'blog_db' if it doesn't already exist.
-- On most shared hosting, including InfinityFree, you will skip this step and use
-- the database name already provided to you (e.g., ifo_XXXX_Blogsite).
-- REMOVED: CREATE DATABASE IF NOT EXISTS blog_db DEFAULT CHARACTER SET utf8mb4;
-- REMOVED: USE blog_db;
-- Note: When importing, ensure you have already selected the correct database (ifo_40281460_Blogsite) in phpMyAdmin.

-- Table user
-- This table stores all user authentication and profile data.
-- Required Columns: id, username, email, password, role

CREATE TABLE IF NOT EXISTS user (
id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique user identifier',
username VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique username for login and display',
email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Unique email address',
password VARCHAR(255) NOT NULL COMMENT 'Securely stored password hash (using PASSWORD_HASH)',
role VARCHAR(10) NOT NULL DEFAULT 'user' COMMENT 'User role (e.g., "user", "admin")',
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
) ENGINE = InnoDB;

-- Table blogPost
-- This table stores all blog posts and links them to their author.
-- Required Columns: id, user_id, title, content, created_at, updated_at

CREATE TABLE IF NOT EXISTS blogPost (
id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique blog post identifier',
user_id INT UNSIGNED NOT NULL COMMENT 'Foreign Key: Links the post back to the author in the user table',
title VARCHAR(255) NOT NULL COMMENT 'The title of the blog post',
content TEXT NOT NULL COMMENT 'The main content of the blog (Markdown or HTML)',
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of post creation',
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of last update',
PRIMARY KEY (id),
INDEX fk_blogPost_user_idx (user_id ASC),
CONSTRAINT fk_blogPost_user
FOREIGN KEY (user_id)
REFERENCES user (id)
ON DELETE CASCADE  -- Delete post if author is deleted
ON UPDATE CASCADE
) ENGINE = InnoDB;