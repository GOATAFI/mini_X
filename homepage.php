<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/Mini-X/frontend/login.html");
    exit;
}

require 'backend/db.php';

// Fetch all posts from the database
$stmt = $pdo->query("SELECT posts.content, posts.created_at, users.username 
                     FROM posts 
                     JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>

<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="backend/routes/logout.php">Logout</a>

    <h2>Post Something</h2>
    <form action="backend/routes/post.php" method="POST">
        <textarea name="content" rows="4" cols="50" placeholder="What's on your mind?"></textarea>
        <br>
        <button type="submit">Post</button>
    </form>

    <h2>All Posts</h2>
    <?php foreach ($posts as $post): ?>
        <div>
            <strong><?php echo htmlspecialchars($post['username']); ?>:</strong>
            <p><?php echo htmlspecialchars($post['content']); ?></p>
            <small>Posted on <?php echo $post['created_at']; ?></small>
            <hr>
        </div>
    <?php endforeach; ?>
</body>

</html>