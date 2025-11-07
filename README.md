Blog Application: Full-Stack PHP/MySQL

Project Overview

This is a full-stack blog application developed using a modern, structured approach. The front-end uses HTML, CSS (Tailwind CSS for rapid responsiveness), and JavaScript (for interactions). The backend is powered by PHP for business logic, and MySQL/MariaDB for data persistence.

Key Features:

Secure Authentication: User registration, login, and logout using secure password hashing (password_hash).

Authorization: Users can only create, update, or delete their own blog posts.

CRUD Operations: Full functionality to Create, Read (List and Single View), Update, and Delete blog posts.

Responsive UI: Designed for optimal viewing across mobile, tablet, and desktop screens.

1. Local Setup and Installation

1.1. Prerequisites

A web server environment (e.g., XAMPP, MAMP, WAMP, or Docker).

PHP 7.4+

MySQL / MariaDB database.

1.2. Database Setup

Create a new database (e.g., blog_db or use the one provided by your host).

Import the structure using the database_schema.sql file via phpMyAdmin.

<!-- end list -->

# Example command line for importing (use phpMyAdmin for hosting)
mysql -u root -p blog_db < database_schema.sql


1.3. Environment Configuration (Sensitive Data)

Sensitive information (like database credentials) MUST be stored outside of the public-facing code.

Create a file named .env in the root directory (/BlogApp).

Copy the contents from .env.example into your new .env file and replace the placeholder values with your actual database credentials (especially critical when deploying to InfinityFree).

File: .env (Sensitive - Keep Secret!)

DB_HOST=localhost
DB_NAME=blog_db
DB_USER=root
DB_PASS=your_strong_password


1.4. Deployment

When deploying to a hosting platform (like InfinityFree), ensure you update the credentials in your .env file to match the remote host's database configuration before uploading the files. The web root should be set to the public_html directory.

2. Project Structure

The project is structured to separate presentation (public_html) from business logic (backend).

Directory/File

Description

.env

CRITICAL: Stores secure credentials for PHP access.

public_html/

The web root (what the server displays). Contains all entry point PHP/HTML files.

backend/config.php

Initializes the database connection (PDO) and loads environment variables.

backend/auth_handler.php

All user authentication functions (registration, login, authorization checks).

backend/blog_handler.php

NEW: All blog management functions (CRUD operations).

database_schema.sql

SQL script for creating tables.