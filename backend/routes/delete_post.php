<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'] ?? null;

    if (!$postId) {
        echo "Post ID is required.";
        exit;
    }

    // Ensure the post belongs to the logged-in user
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        echo "Post deleted successfully.";
    } else {
        echo "You are not authorized to delete this post.";
    }

    // Redirect back to the homepage
    header("Location: http://localhost/Mini-X/homepage.php");
    exit;
}
