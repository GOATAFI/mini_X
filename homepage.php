<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/Mini-X/frontend/login.html");
    exit;
}

require 'backend/db.php';

$currentUserId = $_SESSION['user_id'];

// Fetch all posts from the database
$stmt = $pdo->query("SELECT posts.id, posts.content, posts.created_at, users.username 
                     FROM posts 
                     JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();


// Fetch users the current user may want to follow
$usersStmt = $pdo->prepare("SELECT * FROM users WHERE id != ?");
$usersStmt->execute([$currentUserId]);
$users = $usersStmt->fetchAll();
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

    <!-- Suggest Users to Follow -->
    <h2>Users You May Want to Follow</h2>
    <?php foreach ($users as $user): ?>
        <div>
            <strong><?php echo htmlspecialchars($user['username']); ?></strong>

            <?php
            // Check if the logged-in user is already following this user
            $checkFollowStmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ? AND followee_id = ?");
            $checkFollowStmt->execute([$currentUserId, $user['id']]);
            $isFollowing = $checkFollowStmt->fetchColumn();
            ?>

            <?php if ($isFollowing): ?>
                <!-- Unfollow Form -->
                <form action="backend/routes/unfollow.php" method="POST">
                    <input type="hidden" name="followee_id" value="<?php echo $user['id']; ?>">
                    <button type="submit">Unfollow</button>
                </form>
            <?php else: ?>
                <!-- Follow Form -->
                <form action="backend/routes/follow.php" method="POST">
                    <input type="hidden" name="followee_id" value="<?php echo $user['id']; ?>">
                    <button type="submit">Follow</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

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

            <h4>Comments:</h4>
            <?php
            // Fetch comments for this post
            $commentStmt = $pdo->prepare("SELECT comments.content, comments.created_at, users.username 
                                      FROM comments 
                                      JOIN users ON comments.user_id = users.id 
                                      WHERE comments.post_id = ? 
                                      ORDER BY comments.created_at ASC");
            $commentStmt->execute([$post['id']]);
            $comments = $commentStmt->fetchAll();
            ?>

            <?php foreach ($comments as $comment): ?>
                <div>
                    <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    <small>Commented on <?php echo $comment['created_at']; ?></small>
                </div>
            <?php endforeach; ?>

            <!-- Comment Form -->
            <form action="backend/routes/comment.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <textarea name="content" rows="2" cols="50" placeholder="Write a comment..."></textarea>
                <br>
                <button type="submit">Comment</button>
            </form>
            <hr>
        </div>
    <?php endforeach; ?>

</body>

</html>