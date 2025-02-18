<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tweet_id']) && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'];
    $tweet_id = $_POST['tweet_id'];
    
    if ($_POST['action'] == "like") {
        $stmt = $conn->prepare("INSERT IGNORE INTO likes (user_id, tweet_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $tweet_id);
        $stmt->execute();
    } elseif ($_POST['action'] == "unlike") {
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND tweet_id = ?");
        $stmt->bind_param("ii", $user_id, $tweet_id);
        $stmt->execute();
    }
}


header("Location: index.php");
exit();
?>
