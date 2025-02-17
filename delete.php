<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $tweet_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah tweet ini milik user yang login
    $stmt = $conn->prepare("SELECT * FROM tweets WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $tweet_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika tweet ditemukan, hapus dari database
        $stmt = $conn->prepare("DELETE FROM tweets WHERE id = ?");
        $stmt->bind_param("i", $tweet_id);
        $stmt->execute();
    }
}

header("Location: index.php");
exit();
?>
