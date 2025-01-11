<?php
header('Content-Type: application/json');

// Simulate the session check (replace with actual session logic)
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require '../db.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$content = $data['content'] ?? '';

if (empty($content)) {
    http_response_code(400);
    echo json_encode(['error' => 'Content cannot be empty']);
    exit;
}

// Insert into database
$stmt = $pdo->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
$stmt->execute([$_SESSION['user_id'], $content]);

echo json_encode(['message' => 'Post created successfully']);
