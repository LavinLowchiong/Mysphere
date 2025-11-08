# MyShpere Blog Platform

**MyShpere** is a lightweight, secure, and user-friendly blogging platform built with PHP, PDO (MySQL), and Tailwind CSS. Users can register, log in, create, edit, and delete blog posts. It supports Markdown and HTML content, with an intuitive interface for both desktop and mobile devices.

---

## Features

### User Authentication

- User registration with email and username.
- Secure login with password hashing (`password_hash()`).
- Session-based authentication.
- Logout functionality.

### Blog Management

- Create new blog posts with a title and content (supports Markdown & HTML).
- Edit or delete posts (only by the author).
- View individual blog posts.
- Preview blogs on the homepage with content snippet.

### UI & Frontend

- Fully responsive interface using **Tailwind CSS**.
- Markdown editor integration via **EasyMDE**.
- Clean, readable typography using **Inter** font.
- Dynamic session messages for success and error notifications.

### Security

- Prepared statements to prevent SQL injection.
- Passwords hashed with PHP `password_hash()`.
- Access control to ensure users can only edit/delete their own posts.
- Session messages sanitized via `htmlspecialchars()`.

---
