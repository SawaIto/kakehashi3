<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>かけはし</title>
</head>
<body>
    <header class="h-26 fixed top-0 left-0 mb-5 w-full bg-white shadow-md z-50 flex flex-col sm:flex-row justify-between items-center px-5 py-2">
        <div class="flex items-center">
            <a href="index.php" class="flex title-font font-medium items-center text-gray-900 mb-2 sm:mb-0">
                <img src="./img/header_logo.png" alt="橋のロゴ" style="width: 90px; height: 60px;" class="mr-4">
            </a>
            <span class="text-base text-gray-900">かけはしへようこそ</span>
        </div>
        <div class="container-fluid">
            <nav class="flex flex-col sm:flex-row">
                <ul class="flex justify-end items-center space-x-6">
                    <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="login.php">ログイン</a></li>
                    <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="register.php">新規管理者登録</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="pt-32"> <!-- メインコンテンツのための余白 -->
</body>