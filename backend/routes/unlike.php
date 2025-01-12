<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

// Remove the like
$stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);

echo json_encode(['success' => 'Post unliked']);
