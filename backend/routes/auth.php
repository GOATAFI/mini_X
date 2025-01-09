<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$username || !$email || !$password) {
        echo "All fields are required.";
        exit;
    }

    // Check if the username or email already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $checkStmt->execute([$username, $email]);
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        echo "Signup failed: Username or email already exists.";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $hashedPassword]);
        echo "Signup successful!";
    } catch (PDOException $e) {
        echo "Signup failed: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
