<?php
// backend/config.php

/**
 * ==========================================================
 * SECTION 1: ENVIRONMENT VARIABLE LOADER (.env Simulation)
 * ==========================================================
 * This class simulates loading variables from a `.env` file,
 * which is a crucial security practice to keep credentials
 * out of version control and public access.
 * * NOTE: For actual deployment, you would need a proper DotEnv library.
 * This simple implementation reads line-by-line from the local .env file.
 */
class DotEnv
{
    protected string $path;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            // Error handling for missing .env file
            error_log("FATAL ERROR: .env file not found at $path. Using default security placeholders.");
            $this->path = ''; // Disable file reading
            return;
        }
        $this->path = $path;
    }

    public function load(): void
    {
        if (empty($this->path)) return;

        // Read the file content
        $lines = file($this.path, FILE_IGNORE_EMPTY_LINES | FILE_SKIP_WHITE_SPACE);

        if (!$lines) {
             error_log("FATAL ERROR: .env file found but is empty or unreadable.");
             return;
        }

        foreach ($lines as $line) {
            // Ignore comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Set environment variable globally
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}

// Load environment variables from the .env file in the project root
// IMPORTANT: Adjust path based on your final deployment structure.
$dotEnv = new DotEnv(__DIR__ . '/../.env'); 
$dotEnv->load();

/**
 * ==========================================================
 * SECTION 2: DATABASE CONNECTION SETUP (PDO)
 * ==========================================================
 * Uses the loaded environment variables for secure connection.
 */

// Retrieve sensitive credentials from environment variables
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost'; // Default fallback if .env fails
$DB_NAME = $_ENV['DB_NAME'] ?? 'blog_db';
$DB_USER = $_ENV['DB_USER'] ?? 'root';
$DB_PASS = $_ENV['DB_PASS'] ?? '';

// Data Source Name (DSN)
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

// PDO options for security and error handling
$options = [
    // Throw exceptions on errors, which is crucial for development and proper error handling
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Disable emulated prepared statements for security against SQL injection
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Set default fetch mode to associative array
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // Attempt to establish the database connection
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (\PDOException $e) {
    // Log the error securely and provide a generic message to the user
    error_log("Database Connection Error: " . $e->getMessage(), 0);
    
    // Provide a proper indication to the user without exposing sensitive details
    die("<h1>Database Connection Failed</h1><p>We are experiencing technical difficulties. Please ensure your `.env` file is correctly configured for the host.</p>");
}