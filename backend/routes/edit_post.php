<?php
session_start();
require '../db.php';

$postId = $_POST['post_id'] ?? null;

if (!$postId) {
    echo "Post ID is required.";
    exit;
}

// Fetch the post details
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$postId, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found or you are not authorized to edit this post.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>

<body>
    <h1>Edit Post</h1>
    <form action="update_post.php" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <textarea name="content" rows="4" cols="50"><?php echo htmlspecialchars($post['content']); ?></textarea>
        <br>
        <button type="submit">Update</button>
    </form>
    <a href="http://localhost/Mini-X/homepage.php">Cancel</a>
</body>

</html>