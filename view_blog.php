<?php
// public_html/view_blog.php - Single Blog View Page

// Load handlers
require_once __DIR__ . '/backend/auth_handler.php';
require_once __DIR__ . '/backend/blog_handler.php';

// Get blog ID from query parameters
$blog_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Check for valid ID
if (!$blog_id) {
    // If no ID is provided or invalid, redirect to the home page
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid blog post ID provided.'];
    header("Location: /index.php");
    exit();
}

// Fetch the blog post
global $pdo;
$blog = get_single_blog($pdo, $blog_id);

// Check if blog exists
if (!$blog) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Blog post not found.'];
    header("Location: /index.php");
    exit();
}

// Authorization check for edit/delete buttons
// Compares the logged-in user's ID with the post's user_id
$is_author = is_authenticated() && (get_current_user_id() === (int)$blog['user_id']);
$current_username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> | Blog App</title>
    <!-- Load Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Load custom styles for better blog content formatting -->
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-indigo-600 shadow-md">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="/index.php" class="text-2xl font-bold text-white tracking-wider">The Blog App</a>
            <a href="/index.php" class="text-white hover:text-indigo-200 transition duration-150 font-medium">
                &larr; Back to Home
            </a>
        </nav>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <?php display_session_message(); ?>

        <article class="bg-white p-8 rounded-xl shadow-2xl">
            
            <header class="border-b pb-4 mb-6">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                    <?php echo htmlspecialchars($blog['title']); ?>
                </h1>
                
                <div class="flex flex-wrap justify-between items-center text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-gray-700">By: <?php echo htmlspecialchars($blog['username']); ?></span>
                    </div>
                    <div>
                        <span class="mr-4">Published: <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                        <?php if ($blog['updated_at'] !== $blog['created_at']): ?>
                            <span>(Updated: <?php echo date('M d, Y', strtotime($blog['updated_at'])); ?>)</span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Blog Content Area (Use custom CSS for formatting) -->
            <section class="blog-content prose max-w-none text-lg leading-relaxed">
                <?php 
                    // WARNING: The content is displayed as raw HTML/Markdown output from the DB.
                    // For security, content should be sanitized or processed by a Markdown parser
                    // before display if user input is not completely trusted.
                    echo $blog['content']; 
                ?>
            </section>
            
            <!-- Edit/Delete Actions (Authorization Required) -->
            <?php if ($is_author): ?>
                <footer class="mt-8 pt-6 border-t flex justify-end space-x-4">
                    <!-- Edit Button -->
                    <a href="/edit_blog.php?id=<?php echo $blog['id']; ?>" 
                       class="py-2 px-4 rounded-lg text-white font-medium bg-indigo-600 hover:bg-indigo-700 transition duration-150 shadow-md">
                        Edit This Post
                    </a>
                    
                    <!-- Delete Form (using POST for security) -->
                    <form id="delete-form" action="/../backend/blog_handler.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                        <button type="submit" 
                                class="py-2 px-4 rounded-lg text-white font-medium bg-red-600 hover:bg-red-700 transition duration-150 shadow-md">
                            Delete Post
                        </button>
                    </form>
                </footer>
            <?php endif; ?>

        </article>
    </main>

</body>
</html>