<?php
session_start();
require 'config.php';

$query = "SELECT tweets.*, COUNT(likes.id) AS total_likes 
          FROM tweets 
          LEFT JOIN likes ON tweets.id = likes.tweet_id 
          GROUP BY tweets.id 
          ORDER BY total_likes DESC 
          LIMIT 10";
$result = mysqli_query($conn, $query);

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Proses posting tweet
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $content = isset($_POST['tweet']) && trim($_POST['tweet']) !== '' ? $_POST['tweet'] : null;


    
    // Upload file jika ada
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

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO tweets (user_id, content, attachment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $content, $attachment);
    $stmt->execute();
    
    // Refresh halaman setelah tweet
    header("Location: index.php");
    exit();
}

// Ambil semua tweet dari database
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
<script>
    var swiper = new Swiper(".swiper-container", {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>
    <style>
        
        body { 
    font-family: Arial, sans-serif; 
    background: linear-gradient(to bottom, rgb(228, 228, 228),rgb(153, 90, 163), rgb(240, 214, 241));

    background-attachment: fixed;  /* Fix background agar tidak patah saat scroll */
    background-size: cover;  /* Pastikan background menyesuaikan layar */
    text-align: center;
    min-height: 100vh;
    margin: 0;
    padding: 0;
}



        .container { width: 60%; margin: auto; padding: 20px; background: white; border-radius: 10px; }
        textarea { width: 90%; min-height: 80px; padding: 10px; border: 2px solid #ba68c8; border-radius: 10px; font-size: 16px; resize: none; }
        .tweet-box { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .tweet { background:rgb(209, 177, 215); padding: 15px; border-radius: 10px; margin: 10px auto; width: 90%; text-align: left; position: relative; }
        .btn { background-color: #ba68c8; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        .like-btn { cursor: pointer; border: none; background: none; color: #880e4f; font-size: 16px; }
        .delete-btn { position: absolute; top: 10px; right: 10px; background: none; border: none; color: red; cursor: pointer; font-size: 14px; }

        
        .comment-section {
    margin-top: 10px;
    padding: 10px;
    background: #fce4ec;
    border-radius: 10px;
}

.comment-section input {
    width: 80%;
    padding: 5px;
    border: 2px solid #ba68c8;
    border-radius: 10px;
}

.comment-btn {
    background: #ba68c8;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 10px;
    cursor: pointer;
}

.comments {
    margin-top: 10px;
}

.comment-bubble {
    background: #fff;
    padding: 10px;
    border-radius: 15px;
    display: inline-block;
    margin: 5px 0;
    max-width: 80%;
    position: relative;
}

.comment-bubble::after {
    content: "";
    position: absolute;
    bottom: -10px;
    left: 15px;
    width: 0;
    height: 0;
    border-top: 10px solid #fff;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
}

.delete-comment-form {
    display: inline;
    margin-left: 10px;
}

.delete-comment-btn {
    background: none;
    border: none;
    color: red;
    cursor: pointer;
    font-size: 12px;
}

.btn {
    background-color: #ba68c8;
    color: white;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 10px;
    font-size: 14px;
    display: inline-block;
    margin-top: 10px;
}

/* Gaya untuk tweet box */
.tweet-box {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0px 5px 15px rgba(166, 74, 201, 0.3);
    text-align: center;
    max-width: 650px;
    margin: auto;
    border: 2px solid #d8a8ff;
}

/* Styling textarea */
textarea {
    width: 90%;
    height: 100px;
    border-radius: 10px;
    border: 2px solid #a64ac9;
    padding: 10px;
    font-size: 14px;
    resize: none;
    outline: none;
    transition: 0.3s;
    background: #fdf4ff;
    color: #6a2c91;
}

textarea:focus {
    border-color: #8c3bbd;
    box-shadow: 0 0 10px rgba(166, 74, 201, 0.4);
}

/* Custom File Upload */
.custom-file-upload {
    display: inline-block;
    padding: 12px 18px;
    border: 2px solid rgba(166, 74, 201, 0.6);
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(245, 225, 252, 0.3), rgba(216, 168, 255, 0.3));
    color: #6a2c91;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s ease-in-out;
    font-size: 14px;
    backdrop-filter: blur(5px); /* Efek kaca */
}

.custom-file-upload:hover {
    background: linear-gradient(135deg, rgba(240, 214, 255, 0.5), rgba(209, 164, 242, 0.5));
    color: white;
    transform: scale(1.05);
    box-shadow: 0px 4px 15px rgba(166, 74, 201, 0.5);
}


/* Menampilkan nama file */
#fileName {
    display: block;
    margin-top: 8px;
    font-size: 14px;
    color: #6a2c91;
    font-weight: bold;
}

/* Tombol Tweet */
.btn {
    background: linear-gradient(135deg, #a64ac9, #6a2c91);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
    font-weight: bold;
    box-shadow: 0px 3px 10px rgba(166, 74, 201, 0.4);
}

.btn:hover {
    background: linear-gradient(135deg, #8c3bbd, #5a1f7a);
    transform: scale(1.05);
    box-shadow: 0px 5px 15px rgba(166, 74, 201, 0.5);
}

.tweet {
    background: rgb(209, 177, 215); 
    padding: 15px; 
    border-radius: 10px; 
    margin: 10px auto; 
    width: 90%; 
    text-align: left; 
    position: relative; 
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Efek hover untuk box postingan */
.tweet:hover {
    transform: translateY(-5px); /* Menarik sedikit ke atas saat hover */
    box-shadow: 0 5px 15px rgba(166, 74, 201, 0.4); /* Menambahkan bayangan saat hover */
    border: 2px solid #d1a0e1;
}




    </style>
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
                <textarea name="tweet" placeholder="What's on your mind? °‧🫧⋆.ೃ࿔*:･" required></textarea><br><br>
                <input type="file" name="attachment" class="custom-file-upload"><br><br>
                <button type="submit" class="btn">Tweet</button>
            </form>
        </div>
        <h2>Welcome to Tweetly</h2>

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="tweet">
                <p>
                <strong><?= htmlspecialchars($row['username']); ?></strong>: 
                <?= htmlspecialchars($row['content']); ?>
    <br>
    <small style="color: gray;">
        🕒 <?= date("d M Y, H:i", strtotime($row['created_at'])); ?>
    </small>
</p>
                <?php if ($row['attachment']): ?>
                    <img src="<?= $row['attachment']; ?>" width="200"><br>
                <?php endif; ?>

                <!-- Ambil jumlah like -->
                <?php
                $tweet_id = $row['id'];
                $like_result = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE tweet_id = $tweet_id");
                $like_count = $like_result->fetch_assoc()['like_count'];

                // Cek apakah user sudah like
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

                <!-- Tampilkan tombol hapus jika ini tweet milik user yang login -->
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