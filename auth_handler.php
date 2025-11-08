<?php
// backend/auth_handler.php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load database connection
require_once __DIR__ . '/config.php';

/**
 * =============================
 * SESSION & AUTHENTICATION HELPERS
 * =============================
 */

/**
 * Check if a user is logged in
 * @return bool
 */
function is_authenticated(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function get_current_user_id(): ?int {
    return is_authenticated() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Require login for pages
 * @param string $redirect_to
 */
function require_login(string $redirect_to = '/login.php'): void {
    if (!is_authenticated()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'You must be logged in to access this page.'];
        header("Location: " . $redirect_to);
        exit();
    }
}

/**
 * Display session messages
 */
function display_session_message(): void {
    if (isset($_SESSION['message'])) {
        $msg = $_SESSION['message'];
        $type = $msg['type'];
        $text = htmlspecialchars($msg['text']);

        $class = match($type) {
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            default => 'bg-blue-100 border-blue-400 text-blue-700',
        };

        echo "<div class='message p-4 mb-4 rounded-lg text-center border {$class} transition duration-300'>";
        echo $text;
        echo "</div>";

        unset($_SESSION['message']);
    }
}

/**
 * =============================
 * USER REGISTRATION
 * =============================
 */
function register_user(PDO $pdo, string $username, string $email, string $password): bool {
    try {
        // Validate password length
        if (strlen($password) < 6) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Password must be at least 6 characters long.'];
            return false;
        }

        // Check for duplicate username or email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Username or email already taken.'];
            return false;
        }

        // Hash password and insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Registration successful! You can now log in.'];
        return true;

    } catch (PDOException $e) {
        error_log("Registration Error: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'A database error occurred during registration.'];
        return false;
    }
}

/**
 * =============================
 * USER LOGIN
 * =============================
 */
function login_user(PDO $pdo, string $username, string $password): bool {
    try {
        $stmt = $pdo->prepare("SELECT id, password FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid username or password.'];
            return false;
        }

        // Set session
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $username;

        return true;

    } catch (PDOException $e) {
        error_log("Login Error: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'A database error occurred during login.'];
        return false;
    }
}

/**
 * =============================
 * LOGOUT FUNCTIONALITY
 * =============================
 */
function logout_user(): void {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
}

