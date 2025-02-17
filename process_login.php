<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data user berdasarkan username
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Cek password
        if (password_verify($password, $user['password'])) {
            // Simpan sesi login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect ke index.php
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location.href='login.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
