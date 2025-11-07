<?php
// public_html/create_blog.php - Blog Editor for New Posts

// Load handlers and enforce login requirement
require_once __DIR__ . '/backend/auth_handler.php';
require_once __DIR__ . '/backend/blog_handler.php';

// Authorization: Only logged-in users can create blogs
// If not logged in, this function redirects the user to /login.php with an error message.
require_login('/login.php');

$blog = [
    'title' => '',
    'content' => '',
    'form_action' => 'create'
];

$page_title = 'Create New Blog Post';
$button_text = 'Publish Post';

// Note: If you want to retain form data after a failed submission (e.g., if a validation error occurs
// in blog_handler.php), you would need to store the failed POST data in a session variable
// and populate the form fields ($blog['title'], $blog['content']) from that session data here.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Load Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    
    <!-- === EASYMDE (Markdown Editor) ASSETS === -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <!-- ======================================= -->
</head>
<body class="bg-gray-100 min-h-screen">

    <header class="bg-indigo-600 shadow-md">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="/index.php" class="text-2xl font-bold text-white tracking-wider">The Blog App</a>
        </nav>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo htmlspecialchars($page_title); ?></h1>

        <?php display_session_message(); ?>

        <div class="bg-white p-6 rounded-xl shadow-lg">
            
            <!-- Form submits to the blog_handler.php for processing -->
            <form action="/../backend/blog_handler.php" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="<?php echo htmlspecialchars($blog['form_action']); ?>">
                <!-- No blog_id needed for creation -->

                <div>
                    <label for="title" class="block text-lg font-semibold text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo htmlspecialchars($blog['title']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg text-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition duration-150"
                           placeholder="Enter your blog title">
                </div>

                <div>
                    <label for="content" class="block text-lg font-semibold text-gray-700 mb-2">Content (Markdown or HTML allowed)</label>
                    <textarea id="content" name="content" rows="15" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-inner resize-y transition duration-150"
                              placeholder="Start writing your amazing blog post here."><?php echo htmlspecialchars($blog['content']); ?></textarea>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="/index.php" class="py-3 px-6 rounded-lg text-gray-600 hover:bg-gray-200 transition duration-150 font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            class="py-3 px-6 border border-transparent rounded-lg shadow-md text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 transform hover:scale-[1.01]">
                        <?php echo htmlspecialchars($button_text); ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <!-- Load custom JavaScript to initialize the Markdown editor -->
    <script src="/assets/script.js"></script>

</body>
</html>