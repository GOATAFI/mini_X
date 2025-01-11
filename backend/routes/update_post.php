<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;
    $content = $_POST['content'] ?? null;

    if (!$postId || !$content) {
        echo "Post ID and content are required.";
        exit;
    }

    // Ensure the post belongs to the logged-in user
    $stmt = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$content, $postId, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        echo "Post updated successfully.";
    } else {
        echo "You are not authorized to edit this post.";
    }

    // Redirect back to the homepage
    header("Location: http://localhost/Mini-X/homepage.php");
    exit;
}
