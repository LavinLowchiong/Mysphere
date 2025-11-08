<?php
// public_html/logout.php

require_once __DIR__ . '/backend/auth_handler.php';

// Log out the current user
logout_user();

// Redirect to login page
header("Location: /login.php");
exit();
