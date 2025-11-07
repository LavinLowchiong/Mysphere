<?php
// backend/auth_handler.php

// Ensure session is started before any output is sent
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load the database configuration and connection object ($pdo)
// Note: Path is relative from public_html pages, so we use `../backend/config.php` 
// when including from a public page, but the handlers include each other directly.
require_once __DIR__ . '/config.php';

/**
 * Checks if the current user is authenticated.
 * @return bool True if authenticated, false otherwise.
 */
function is_authenticated(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Returns the currently logged-in user's ID.
 * @return int|null The user ID or null if not logged in.
 */
function get_current_user_id(): ?int {
    return is_authenticated() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Redirects the user to the login page if not authenticated.
 * Used for pages requiring login (e.g., create, edit).
 * @param string $redirect_to The URL of the login page (default /login.php).
 */
function require_login(string $redirect_to = '/login.php'): void {
    if (!is_authenticated()) {
        // Use a session variable to pass a message to the login page
        $_SESSION['message'] = ['type' => 'error', 'text' => 'You must be logged in to access this page.'];
        header("Location: " . $redirect_to);
        exit();
    }
}

/**
 * Displays any session message (for error or success) and clears it.
 */
function display_session_message(): void {
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        $type = $msg['type'];
        $text = htmlspecialchars($msg['text']);
        
        // Tailwind CSS styling for messages
        $class = match($type) {
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            default => 'bg-blue-100 border-blue-400 text-blue-700',
        };
        
        echo "<div class='message p-4 mb-4 rounded-lg text-center border {$class} transition duration-300'>";
        echo $text;
        echo "</div>";
        unset($_SESSION['message']); // Clear message after display
    }
}


/**
 * Handles the user registration process.
 * @param PDO $pdo The PDO database connection object.
 * @param string $username User's chosen username.
 * @param string $email User's email.
 * @param string $password User's chosen password (plain text).
 * @return bool True on success, false on failure.
 */
function register_user(PDO $pdo, string $username, string $email, string $password): bool {
    try {
        // Input validation
        if (strlen($password) < 6) {
             $_SESSION['message'] = ['type' => 'error', 'text' => 'Password must be at least 6 characters long.'];
             return false;
        }

        // Check if username or email already exists (using prepared statements for security)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Username or Email already taken.'];
            return false;
        }

        // Securely hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the insertion query
        $stmt = $pdo->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Registration successful! You can now log in.'];
        return true;

    } catch (\PDOException $e) {
        error_log("Registration Error: " . $e->getMessage());
        $_SESSION['message'] = ['type's' => 'error', 'text' => 'A database error occurred during registration.'];
        return false;
    }
}

/**
 * Handles the user login process.
 * @param PDO $pdo The PDO database connection object.
 * @param string $username The username provided by the user.
 * @param string $password The plain text password provided by the user.
 *Read more