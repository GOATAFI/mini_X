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
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Card Container -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 text-center">Edit Post</h1>
                <p class="mt-2 text-center text-gray-600">Make your changes below</p>
            </div>

            <!-- Edit Form -->
            <form action="update_post.php" method="POST" class="space-y-6">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">

                <div>
                    <textarea
                        name="content"
                        rows="6"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 resize-none shadow-sm"
                        placeholder="What's on your mind?"><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>

                <!-- Buttons Container -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a
                        href="http://localhost/Mini-X/homepage.php"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-sm font-medium">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 text-sm font-medium">
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>