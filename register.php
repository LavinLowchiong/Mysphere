<?php
// public_html/register.php - Registration Form

require_once __DIR__ . '/backend/auth_handler.php';
session_start();

// Redirect if already logged in
if (is_authenticated()) {
    header("Location: /index.php");
    exit();
}

// Handle registration POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    global $pdo;

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (register_user($pdo, $username, $email, $password)) {
        // Successful → redirect to login
        header("Location: /login.php");
        exit();
    } else {
        // Failed → repopulate form fields
        $_SESSION['old'] = [
            'username' => $username,
            'email' => $email
        ];
    }
}

// Get old form values if available
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MyShpere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md">
        <div class="bg-white p-8 rounded-xl shadow-2xl">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Create Your Account</h2>
            
            <?php display_session_message(); ?>

            <form action="/register.php" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="register">

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" required
                           value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                           placeholder="Choose a username">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                           placeholder="you@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password (Min 6 chars)</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 transform hover:scale-[1.01]">
                    Register Account
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="/login.php" class="font-medium text-indigo-600 hover:text-indigo-500 transition duration-150">
                        Log in here
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
