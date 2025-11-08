// assets/script.js

/**
 * ==========================================================
 * Markdown Editor Initialization (using EasyMDE)
 * ==========================================================
 * Initializes EasyMDE on pages with a <textarea id="content">.
 * Automatically syncs content to the textarea on submit.
 * Handles CDN load delays gracefully.
 */

document.addEventListener("DOMContentLoaded", function() {
    const contentEditor = document.getElementById("content");
    if (!contentEditor) return; // No editor field found on this page

    // Poll until EasyMDE is loaded (for slow CDN load)
    const waitForMDE = setInterval(() => {
        if (typeof EasyMDE !== "undefined") {
            clearInterval(waitForMDE);

            try {
                const easyMDE = new EasyMDE({
                    element: contentEditor,
                    spellChecker: false,
                    toolbar: [
                        "bold", "italic", "heading", "|",
                        "quote", "unordered-list", "ordered-list", "|",
                        "link", "image", "|", "guide"
                    ],
                    status: false,
                    forceSync: true,
                });

                console.info("EasyMDE initialized successfully.");
            } catch (err) {
                console.error("Error initializing EasyMDE:", err);
                showEditorFallback();
            }
        }
    }, 300);

    // Fallback: notify user if EasyMDE never loads
    setTimeout(() => {
        if (typeof EasyMDE === "undefined") {
            clearInterval(waitForMDE);
            showEditorFallback();
        }
    }, 5000);

    // Display a small message to user if editor fails
    function showEditorFallback() {
        const notice = document.createElement("p");
        notice.textContent = "⚠️ Markdown editor failed to load. Plain text mode enabled.";
        notice.style.color = "#b91c1c";
        notice.style.fontSize = "0.9rem";
        contentEditor.parentNode.insertBefore(notice, contentEditor);
        contentEditor.style.display = "block";
    }
});
