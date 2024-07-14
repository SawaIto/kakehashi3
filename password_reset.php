<?php
include("funcs.php");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>パスワード再設定</title>
</head>
<body class="bg-gray-200 min-h-screen flex flex-col">
    <?php include 'header0.php'; ?>
    
    <main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">パスワード再設定</h2>
            <form action="password_reset_process.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="email">メールアドレス:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="email" type="email" name="email" required placeholder="メールアドレス">
                </div>
                <div class="flex items-center justify-between">
                    <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="リセットリンクを送信">
                </div>
            </form>
            <div class="text-center mt-4">
                <a href="login.php" class="text-center font-bol<?php
include("funcs.php");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>パスワード再設定</title>
</head>
<body class="bg-gray-200 min-h-screen flex flex-col">
    <?php include 'header0.php'; ?>
    
    <main class="flex-grow container mx-auto px-4 py-8 mt-16 sm:mt-20 flex items-center justify-center">
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">パスワード再設定</h2>
            <form action="password_reset_process.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="email">メールアドレス:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="email" type="email" name="email" required placeholder="メールアドレス">
                </div>
                <div class="flex items-center justify-between">
                    <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="リセットリンクを送信">
                </div>
            </form>
            <div class="text-center mt-4">
                <a href="login.php" class="text-center font-bold hover:text-blue-700 text-lg">ログインページに戻る</a>
            </div>
        </div>
    </main>

    <footer class="w-full bg-white shadow-md mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>