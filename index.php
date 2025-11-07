<?php
// public_html/index.php

// Load the session and database handlers
// OLD PATH: require_once __DIR__ . '/../backend/auth_handler.php';
// OLD PATH: require_once __DIR__ . '/../backend/blog_handler.php';
//
// NEW PATH (Fixed):
require_once __DIR__ . '/backend/auth_handler.php';
require_once __DIR__ . '/backend/blog_handler.php';

// Get the database connection
$pdo = get_pdo_connection();

// Fetch all blog posts
$posts = get_all_posts($pdo);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog Home</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Optional: Load custom CSS -->
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <!-- Navigation -->
    <nav class="bg-white p-4 shadow-md sticky top-0 z-10">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-gray-800">MyBlog</a>
            <div class="flex space-x-4">
                <?php if (is_authenticated()): ?>
                    <a href="create_blog.php" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-300">+ New Post</a>
                    <form action="login.php" method="POST" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-300">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-gray-800">Login</a>
                    <a href="register.php" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-300">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto max-w-6xl mt-8 px-4">

        <!-- Session Message -->
        <?php display_session_message(); ?>

        <!-- Blog Post Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <?php if (empty($posts)): ?>
                <div class="col-span-full text-center text-gray-500">
                    <h2 class="text-2xl">No blog posts found.</h2>
                    <p class="mt-2">Why don't you <a href="create_blog.php" class="text-blue-600 hover:underline">create one</a>?</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <!-- Blog Card -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                        <div class="p-6">
                            <!-- Post Meta -->
                            <div class="mb-4">
                                <span class="text-sm text-gray-500"><?php echo htmlspecialchars($post['author_username']); ?></span>
                                <span class="text-sm text-gray-500">&middot;</span>
                                <span class="text-sm text-gray-500"><?php echo (new DateTime($post['created_at']))->format('F j, Y'); ?></span>
                            </div>
                            
                            <!-- Title -->
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                <a href="view_blog.php?id=<?php echo $post['id']; ?>" class="hover:text-blue-600">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h2>
                            
                            <!-- Content Preview -->
                            <p class="text-gray-700 mb-4">
                                <!-- Create a simple text preview (e.g., first 150 chars) -->
                                <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 150)) . '...'; ?>
                            </p>
                            
                            <!-- Read More Link -->
                            <a href="view_blog.php?id=<?php echo $post['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium transition duration-300">
                                Read More &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-12 py-6">
        <div class="container mx-auto text-center">
            <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> MyBlog. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>