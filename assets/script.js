// public_html/assets/script.js

/**
 * ==========================================================
 * Markdown Editor Initialization (using EasyMDE)
 * ==========================================================
 * This script runs after the document loads and targets the 
 * 'content' textarea on the create_blog.php and edit_blog.php pages.
 * * NOTE: The necessary EasyMDE library and CSS are loaded directly 
 * in the HTML headers of the PHP files.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if the content textarea exists (it will on create_blog.php and edit_blog.php)
    const contentEditor = document.getElementById('content');

    if (contentEditor && typeof EasyMDE !== 'undefined') {
        try {
            // Initialize the EasyMDE editor instance
            const easyMDE = new EasyMDE({
                element: contentEditor,
                spellChecker: false, // Turn off for cleaner input
                toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "link", "image", "|", "guide"],
                status: false,
                forceSync: true, // Ensure textarea is updated on form submit
            });
            console.log("EasyMDE initialized on content field.");

        } catch (e) {
            console.error("Error initializing EasyMDE:", e);
        }
    }
});

// You can add other client-side logic here, like AJAX functions or client-side form validation.