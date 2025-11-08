<?php
// public_html/index.php - Home / Blog List Page

require_once __DIR__ . '/backend/auth_handler.php';
require_once __DIR__ . '/backend/blog_handler.php';
require_once __DIR__ . '/backend/config.php'; // for $pdo

// Fetch all blogs
$blogs = get_all_blogs($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShpere - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <?php
    // Include the navbar with login/logout functionality
    ?>
    <header class="bg-indigo-600 shadow-md">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <a href="/index.php" class="text-2xl font-bold text-white tracking-wider">MyShpere</a>
        <div class="flex items-center space-x-4">
            <?php if (is_authenticated()): ?>
                <a href="/create_blog.php" 
                   class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded transition duration-150 font-medium">
                    Create Blog
                </a>
                <span class="text-white font-medium">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="/logout.php" 
                   class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition duration-150 font-medium">
                    Logout
                </a>
            <?php else: ?>
                <a href="/login.php" 
                   class="text-white hover:text-indigo-200 transition duration-150 font-medium">
                    Login
                </a>
                <a href="/register.php" 
                   class="text-white bg-indigo-500 hover:bg-indigo-600 px-3 py-1 rounded transition duration-150 font-medium">
                    Register
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php display_session_message(); ?>

        <h1 class="text-4xl font-bold text-gray-800 mb-8">Latest Blogs</h1>

        <div class="space-y-6">
            <?php if (!empty($blogs)): ?>
                <?php foreach ($blogs as $blog): ?>
                    <?php
                        $content_preview = isset($blog['content']) ? strip_tags($blog['content']) : '';
                        if (strlen($content_preview) > 250) {
                            $content_preview = substr($content_preview, 0, 250) . '...';
                        }
                        $author = $blog['author'] ?? 'Unknown';
                    ?>
                    <article class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-200">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                            <a href="/view_blog.php?id=<?php echo $blog['id']; ?>" class="hover:text-indigo-600">
                                <?php echo htmlspecialchars($blog['title'] ?? 'Untitled'); ?>
                            </a>
                        </h2>
                        <div class="text-sm text-gray-500 mb-4">
                            By: <?php echo htmlspecialchars($author); ?> | Published: <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                        </div>
                        <p class="text-gray-700"><?php echo htmlspecialchars($content_preview); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600">No blogs have been posted yet.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
