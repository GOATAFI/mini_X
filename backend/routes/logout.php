<?php
session_start();

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

header("Location: http://localhost/Mini-X/frontend/login.html");
exit;
