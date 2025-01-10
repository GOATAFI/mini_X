<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to post.";
        exit;
    }

    $content = $_POST['content'] ?? null;

    if (!$content) {
        echo "Content cannot be empty.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
    try {
        $stmt->execute([$_SESSION['user_id'], $content]);
        echo "Post added successfully!";
        header("Location: http://localhost/Mini-X/homepage.php");
        exit;
    } catch (PDOException $e) {
        echo "Failed to add post: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
