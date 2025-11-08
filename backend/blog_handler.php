<?php
// backend/blog_handler.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_handler.php';

/**
 * ==========================================================
 * BLOG MANAGEMENT HANDLER
 * Handles CRUD operations for the 'blogPost' table.
 * ==========================================================
 */

/**
 * Create a new blog post.
 */
function create_blog($pdo, $user_id, $title, $content) {
    try {
        if (empty(trim($title)) || empty(trim($content))) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Title and content cannot be empty.'];
            return false;
        }

        $stmt = $pdo->prepare("INSERT INTO blogPost (user_id, title, content, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, $title, $content]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Blog created successfully!'];
        return true;
    } catch (PDOException $e) {
        error_log("Blog Create Error: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'A database error occurred while creating the blog.'];
        return false;
    }
}

/**
 * Fetch all blogs.
 */
function get_all_blogs($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT b.id, b.title, b.content, b.created_at, u.username AS author
            FROM blogPost b
            JOIN user u ON b.user_id = u.id
            ORDER BY b.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Blog Fetch Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch a single blog post by ID.
 */
function get_blog_by_id($pdo, $id) {
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, u.username AS author
            FROM blogPost b
            JOIN user u ON b.user_id = u.id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log("Blog Fetch Single Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Update a blog post (only by the owner).
 */
function update_blog($pdo, $blog_id, $user_id, $title, $content) {
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM blogPost WHERE id = ?");
        $stmt->execute([$blog_id]);
        $owner_id = $stmt->fetchColumn();

        if ($owner_id != $user_id) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Unauthorized: You cannot edit this blog.'];
            return false;
        }

        $stmt = $pdo->prepare("UPDATE blogPost SET title = ?, content = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $content, $blog_id]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Blog updated successfully.'];
        return true;
    } catch (PDOException $e) {
        error_log("Blog Update Error: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'A database error occurred while updating the blog.'];
        return false;
    }
}

/**
 * Delete a blog post (only by the owner).
 */
function delete_blog($pdo, $blog_id, $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM blogPost WHERE id = ?");
        $stmt->execute([$blog_id]);
        $owner_id = $stmt->fetchColumn();

        if ($owner_id != $user_id) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Unauthorized: You cannot delete this blog.'];
            return false;
        }

        $stmt = $pdo->prepare("DELETE FROM blogPost WHERE id = ?");
        $stmt->execute([$blog_id]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Blog deleted successfully.'];
        return true;
    } catch (PDOException $e) {
        error_log("Blog Delete Error: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'A database error occurred while deleting the blog.'];
        return false;
    }
}

/**
 * ==========================================================
 * POST REQUEST HANDLER
 * Handles create, update, delete requests.
 * ==========================================================
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login('/login.php');
    $user_id = get_current_user_id();
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            if (create_blog($pdo, $user_id, $title, $content)) {
                header("Location: /index.php");
                exit();
            } else {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            break;

        case 'update':
            $blog_id = (int)($_POST['blog_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            if (update_blog($pdo, $blog_id, $user_id, $title, $content)) {
                header("Location: /view_blog.php?id=" . $blog_id);
                exit();
            } else {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            break;

        case 'delete':
            $blog_id = (int)($_POST['blog_id'] ?? 0);
            if (delete_blog($pdo, $blog_id, $user_id)) {
                header("Location: /index.php");
                exit();
            } else {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            break;

        default:
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid action.'];
            header("Location: /index.php");
            exit();
    }
}
