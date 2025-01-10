<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $postId = $_POST['post_id'] ?? null;
    $content = $_POST['content'] ?? null;

    if (!$userId || !$postId || !$content) {
        echo "All fields are required.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$postId, $userId, $content]);
        header("Location: http://localhost/Mini-X/homepage.php");
        exit;
    } catch (PDOException $e) {
        echo "Failed to add comment: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
