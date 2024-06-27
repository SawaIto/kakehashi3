<?php
session_start();
require_once 'funcs.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">登録完了</h1>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "<p class='text-green-500 mb-4'>" . h($_SESSION['success_message']) . "</p>";
            unset($_SESSION['success_message']);
        }
        ?>
        <a href="login.php" class="bg-blue-500 text-white px-4 py-2 rounded text-lg">ログインページへ</a>
    </div>
</body>
</html>