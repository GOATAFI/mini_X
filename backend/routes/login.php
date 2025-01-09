<?php
require '../db.php';
session_start(); // Start the session to store login information

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = $_POST['login'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$login || !$password) {
        echo "All fields are required.";
        exit;
    }

    // Check if the input is an email or a username
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        // Input is an email, check the users table by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$login]);
    } else {
        // Input is a username, check the users table by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$login]);
    }

    // Fetch the user record
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, create a session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo "Login successful!";
        // Redirect to the homepage or dashboard
        // header("Location: index.php");
        exit;
    } else {
        echo "Invalid username/email or password.";
    }
} else {
    echo "Invalid request method.";
}
