<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $content = isset($_POST['tweet']) && trim($_POST['tweet']) !== '' ? $_POST['tweet'] : null;


    
    $attachment = null;
    if (!empty($_FILES['attachment']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file);
        $attachment = $target_file;
    }

    $stmt = $conn->prepare("INSERT INTO tweets (user_id, content, attachment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $content, $attachment);
    $stmt->execute();
    
    header("Location: index.php");
    exit();
}

$result = $conn->query("SELECT tweets.*, users.username FROM tweets JOIN users ON tweets.user_id = users.id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tweetly</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">

</head>
<body>
    <h1>Tweetly </h1>
    <div style="text-align: center; padding: 0px;">
    <p>👋🏻 Halo, <?= $_SESSION['username']; ?>! 
        <a href="logout.php" class="btn">Logout 🚪</a>
    </p>
</div>

</div>
    <div class="container">
        <div class="tweet-box">
            <form method="POST" enctype="multipart/form-data">
                <textarea name="tweet" placeholder="What's on your mind? °‧🫧⋆.ೃ࿔*:･" required ></textarea><br><br>
                <input type="file" name="attachment" class="custom-file-upload"><br><br>
                <button type="submit" class="btn">Tweet</button>
            </form>
        </div>
        <h2>Welcome to Tweetly</h2>

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="tweet">
             <p>
                <strong><?= htmlspecialchars($row['username']); ?></strong>: 
                <?= htmlspecialchars($row['content']); ?><br>
                <small style="color: gray;">🕒<?= date("d M Y, H:i", strtotime($row['created_at'])); ?></small>
             </p>
                <?php if ($row['attachment']): ?>
                    <img src="<?= $row['attachment']; ?>" width="200"><br>
                <?php endif; ?>

                <?php
                $tweet_id = $row['id'];
                $like_result = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE tweet_id = $tweet_id");
                $like_count = $like_result->fetch_assoc()['like_count'];

                $user_id = $_SESSION['user_id'];
                $user_like_result = $conn->query("SELECT COUNT(*) AS user_liked FROM likes WHERE tweet_id = $tweet_id AND user_id = $user_id");
                $user_liked = $user_like_result->fetch_assoc()['user_liked'];
                ?>
                <form action="like.php" method="POST">
                    <input type="hidden" name="tweet_id" value="<?= $tweet_id; ?>">
                    <?php if ($user_liked): ?>
                        <button type="submit" name="action" value="unlike" class="like-btn">💔 Unlike (<?= $like_count; ?>)</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="like" class="like-btn">❤️ Like (<?= $like_count; ?>)</button>
                    <?php endif; ?>
                </form>

                <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                    <form action="delete.php" method="GET">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <button type="submit" class="delete-btn" onclick="return">🫧❌🫧</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html> 