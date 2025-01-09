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

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); //PASSWORD_BCRYPT is used to create new password hashes using the CRYPT_BLOWFISH algorithm. This will always result in a hash using the "$2y$" crypt format, which is always 60 characters wide.

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
