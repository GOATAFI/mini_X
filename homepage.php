<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/Mini-X/frontend/login.html");
    exit;
}

require 'backend/db.php';

$currentUserId = $_SESSION['user_id'];

$stmt = $pdo->query("SELECT posts.id, posts.content, posts.created_at, posts.user_id, users.username 
                     FROM posts 
                     JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();

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
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                </h1>
                <a href="backend/routes/logout.php"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Sidebar - Users to Follow -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Users You May Want to Follow</h2>
                    <div class="space-y-4">
                        <?php foreach ($users as $user): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <strong class="text-gray-700"><?php echo htmlspecialchars($user['username']); ?></strong>
                                <?php
                                $checkFollowStmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ? AND followee_id = ?");
                                $checkFollowStmt->execute([$currentUserId, $user['id']]);
                                $isFollowing = $checkFollowStmt->fetchColumn();
                                ?>
                                <?php if ($isFollowing): ?>
                                    <form action="backend/routes/unfollow.php" method="POST">
                                        <input type="hidden" name="followee_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit"
                                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                                            Unfollow
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="backend/routes/follow.php" method="POST">
                                        <input type="hidden" name="followee_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                                            Follow
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="md:col-span-2">
                <!-- Create Post Section -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Create a Post</h2>
                    <form action="backend/routes/post.php" method="POST">
                        <textarea name="content"
                            rows="4"
                            class="w-full p-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="What's on your mind?"></textarea>
                        <button type="submit"
                            class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                            Post
                        </button>
                    </form>
                </div>

                <!-- Posts Feed -->
                <div class="space-y-8">
                    <?php foreach ($posts as $post): ?>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        <?php echo htmlspecialchars($post['username']); ?>
                                    </h3>
                                    <p class="text-gray-500 text-sm">
                                        Posted on <?php echo $post['created_at']; ?>
                                    </p>
                                </div>
                                <?php if ($post['user_id'] === $currentUserId): ?>
                                    <div class="flex space-x-2">
                                        <form action="backend/routes/edit_post.php" method="POST">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm transition duration-200">
                                                Edit
                                            </button>
                                        </form>
                                        <form action="backend/routes/delete_post.php" method="POST">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this post?');"
                                                class="bg-red-100 hover:bg-red-200 text-red-600 px-4 py-2 rounded-lg text-sm transition duration-200">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($post['content']); ?></p>

                            <!-- Comments Section -->
                            <div class="border-t pt-4">
                                <h4 class="font-semibold text-gray-700 mb-4">Comments</h4>
                                <?php
                                $commentStmt = $pdo->prepare("SELECT comments.content, comments.created_at, users.username 
                                                          FROM comments 
                                                          JOIN users ON comments.user_id = users.id 
                                                          WHERE comments.post_id = ? 
                                                          ORDER BY comments.created_at ASC");
                                $commentStmt->execute([$post['id']]);
                                $comments = $commentStmt->fetchAll();
                                ?>

                                <div class="space-y-4 mb-4">
                                    <?php foreach ($comments as $comment): ?>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex justify-between items-start">
                                                <strong class="text-gray-700">
                                                    <?php echo htmlspecialchars($comment['username']); ?>
                                                </strong>
                                                <small class="text-gray-500">
                                                    <?php echo $comment['created_at']; ?>
                                                </small>
                                            </div>
                                            <p class="text-gray-600 mt-2">
                                                <?php echo htmlspecialchars($comment['content']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Comment Form -->
                                <form action="backend/routes/comment.php" method="POST" class="mt-4">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <textarea name="content"
                                        rows="2"
                                        class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Write a comment..."></textarea>
                                    <button type="submit"
                                        class="mt-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                                        Comment
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>