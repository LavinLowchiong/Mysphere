<?php
// public_html/login.php - Login Form

require_once __DIR__ . '/backend/auth_handler.php';
require_once __DIR__ . '/backend/config.php'; // needed for $pdo

// If already logged in, redirect to index
if (is_authenticated()) {
    header("Location: /index.php");
    exit();
}

// Handle POST request for login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username_or_email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username_or_email && $password) {
        if (login_user($pdo, $username_or_email, $password)) {
            // Login successful → redirect to index or dashboard
            header("Location: /index.php");
            exit();
        } else {
            // login_user() sets the session error message automatically
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Please enter both username/email and password.'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MyShpere</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md">
    <div class="bg-white p-8 rounded-xl shadow-2xl">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Welcome Back</h2>

        <?php display_session_message(); ?>

        <form action="/login.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="login">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username or Email</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                       placeholder="Enter your username or email">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 transform hover:scale-[1.01]">
                Log In
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p class="text-gray-600">
                Don't have an account? 
                <a href="/register.php" class="font-medium text-indigo-600 hover:text-indigo-500 transition duration-150">
                    Register here
                </a>
            </p>
            <p class="mt-2">
                <a href="/index.php" class="text-gray-500 hover:text-gray-700 transition duration-150">
                    &larr; Back to Home
                </a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
