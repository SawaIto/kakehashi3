<?php
require_once 'funcs.php';

sschk();

// データベース接続
$pdo = db_conn();

// ユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// グループ情報を取得
$stmt = $pdo->prepare("SELECT * FROM `groups` WHERE id = ?");
$stmt->execute([$_SESSION['group_id']]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }
    </style>
</head>

<body class="bg-blue-50">
    <main class="flex items-center justify-center py-8">
        <div class="container mx-auto m-2 p-2 bg-white rounded shadow-md max-w-md">
            <div class="container mx-auto px-4 py-2 sm:py-4 flex justify-center items-center">
                <img src="./img/header_logo.png" alt="かけ橋ロゴ" class="h-26 sm:h-24">
            </div>
            <h1 class="text-xl font-bold my-3 text-center">ようこそ、<?= h($user['username']) ?>さん</h1>
            <div class="flex justify-between items-center mb-4">
                <p class="text-base">グループ名: <?= h($group['name']) ?></p>
                <?php if ($user['role'] == 'admin' || $user['role'] == 'modify') : ?>
                <a href="mypage.php" class="bg-gray-300 hover:bg-gray-500 text-black font-bold px-3 py-2 rounded text-sm transition duration-300">マイページ</a>
                <?php else : ?>
                        <div></div>
                    <?php endif; ?>
            </div>
            <div class="flex justify-between items-center mb-6">
                <p class="text-base">ユーザー権限: <?= h($user['role']) ?></p>
                <a href="logout.php" class="bg-red-300 hover:bg-red-500 text-black font-bold px-3 py-2 rounded text-sm transition duration-300">ログアウト</a>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <?php if ($user['role'] == 'admin') : ?>
                        <a href="user_register.php" class="block bg-green-300 hover:bg-green-500 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">ユーザー登録</a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                    <?php if ($user['role'] == 'admin' || $user['role'] == 'modify') : ?>
                        <a href="user_list.php" class="block bg-green-500 hover:bg-green-300 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">ユーザー一覧</a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <?php if ($user['role'] == 'admin' || $user['role'] == 'modify') : ?>
                        <a href="schedule_input.php" class="block bg-blue-300 hover:bg-blue-500 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">予定登録</a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                    <a href="schedule_view.php" class="block bg-blue-500 hover:bg-blue-300 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">予定表示</a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <?php if ($user['role'] == 'admin' || $user['role'] == 'modify') : ?>
                        <a href="memo_input.php" class="block bg-orange-300 hover:bg-orange-500 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">メモ登録</a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                    <a href="memo_view.php" class="block bg-orange-500 hover:bg-orange-300 text-black font-bold px-3 py-2.5 rounded text-sm  sm:text-xl text-center transition duration-300">メモ表示</a>
                    <a href="photo_upload.php" class="block bg-pink-300 hover:bg-pink-500 text-black font-bold px-3 py-2.5 rounded text-sm  sm:text-xl text-center transition duration-300">写真追加</a>
                    <a href="photo_view.php" class="block bg-pink-500 hover:bg-pink-300 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">写真表示</a>
                    <a href="album_create.php" class="block bg-purple-300 hover:bg-purple-500 text-black font-bold px-3 py-2.5 rounded text-sm  sm:text-xl text-center transition duration-300">アルバム作成</a>
                    <a href="album_view.php" class="block bg-purple-500 hover:bg-purple-300 text-black font-bold px-3 py-2.5 rounded text-sm sm:text-xl text-center transition duration-300">アルバム表示</a>
                    <a href="chat_create.php" class="block bg-purple-300 hover:bg-purple-500 text-black font-bold px-3 py-2.5 rounded text-sm  sm:text-xl text-center transition duration-300">チャット作成</a>
                    <a href="chat_list.php" class="block bg-purple-300 hover:bg-purple-500 text-black font-bold px-3 py-2.5 rounded text-sm  sm:text-xl text-center transition duration-300">チャット一覧</a>

                </div>
            </div>
        </div>
    </main>
    <footer class="mt-auto py-4">
        <p class="text-sm text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>
    </footer>
</body>

</html>
