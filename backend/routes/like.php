<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

// Check if the post is already liked
$stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$like = $stmt->fetch();

if (!$like) {
    // Add a new like
    $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $stmt->execute([$post_id, $user_id]);
    header("Location: http://localhost/Mini-X/homepage.php"); // Redirect to homepage
    echo json_encode(['success' => 'Post liked']);
} else {
    echo json_encode(['error' => 'Already liked']);
}
