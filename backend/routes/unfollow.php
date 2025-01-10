<?php
require '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $followee_id = $_POST['followee_id'] ?? null;
    $follower_id = $_SESSION['user_id'] ?? null;

    if (!$follower_id || !$followee_id) {
        echo "Invalid input.";
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followee_id = ?");
    try {
        $stmt->execute([$follower_id, $followee_id]);
        header("Location: http://localhost/Mini-X/homepage.php"); // Redirect to homepage
        exit;
    } catch (PDOException $e) {
        echo "Failed to unfollow: " . $e->getMessage();
    }
}
