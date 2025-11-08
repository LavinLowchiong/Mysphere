<?php
// backend/config.php

class DotEnv
{
    protected $path;

    public function __construct($path)
    {
        if (!file_exists($path)) {
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
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lines) {
            error_log("FATAL ERROR: .env file found but is empty or unreadable.");
            return;
        }

        foreach ($lines as $line) {
            // Ignore comments
            if (strpos(trim($line), '#') === 0) {
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

// Load environment variables from the .env file
$dotEnv = new DotEnv(dirname(__DIR__) . '/.env');
$dotEnv->load();

// Retrieve credentials
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? 'blog_db';
$DB_USER = $_ENV['DB_USER'] ?? 'root';
$DB_PASS = $_ENV['DB_PASS'] ?? '';

// PDO setup
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("<h1>Database Connection Failed</h1><p>Please check your .env configuration.</p>");
}
