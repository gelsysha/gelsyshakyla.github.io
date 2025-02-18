<?php
session_start();
require 'config.php';


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tweetly</title>
    <style>
        body {
            background: linear-gradient(135deg,rgb(241, 230, 243),rgb(107, 62, 114));
            font-family: 'Poppins', sans-serif;
            text-align: center;
            color:white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .login-container {
            background: rgb(188, 150, 196);
            padding: 30px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .login-container img {
            width: 80px;
            margin-bottom: 10px;
        }
        input {
            width: 85%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solidrgb(188, 150, 196);
            border-radius: 10px;
            font-size: 16px;
            text-align: center;
        }
        .btn {
            background: #ba68c8;
            color: white;
            border: none;
            padding: 10px;
            width: 90%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #9c27b0;
        }
        a {
            color: #ba68c8;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
    <img src="logocat.jpeg" class="logo" alt="Logo">
        <h2>𝕨𝕖𝕝𝕔𝕠𝕞𝕖 𝕥𝕠 𝕥𝕨𝕖𝕖𝕥𝕝𝕪</h2>
        <form action="process_login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Create one !</a></p>
    </div>
    <?php if (isset($_GET['signup_success'])): ?>
    <p style="color: green;">✅ Successfully signed up! You can now log in.</p>
<?php endif; ?>

</body>
</html>
