<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    if (empty($category) || empty($title) || empty($content)) {
        $error = "カテゴリー選択、表題、内容は必須です。";
    } else {
        $stmt = $pdo->prepare("INSERT INTO memos (user_id, category, title, content) VALUES (?, ?, ?, ?)");
        $status = $stmt->execute([$user_id, $category, $title, $content]);

        if ($status) {
            $_SESSION['success_message'] = "メモが保存されました。";
            header("Location: memo_view.php");
            exit();
        } else {
            $error = "メモの保存に失敗しました。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メモ入力</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto pt-3 pb-6 max-w-md">
        <div class="flex items-center mb-4 justify-center">
            <img src="./img/cork_board_paper6.png" alt="" class="w-8 h-8 mr-4">
            <h2 class="text-2xl font-bold">メモ入力</h2>
        </div>
        <div class="bg-white rounded-lg p-4">
            <h3 class="text-xl font-bold mb-4 bg-yellow-200 text-center rounded px-2 py-1">メモしたい内容を入力</h3>
            <?php if (isset($error)): ?>
                <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="category" class="block font-bold mb-2">カテゴリー:</label>
                    <select id="category" name="category" class="border border-gray-300 rounded-md w-full py-2 px-3">
                        <option value="" disabled selected>選択してください</option>
                        <option value="買い物">買い物</option>
                        <option value="予定">予定</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
                <div>
                    <label for="title" class="block font-bold mb-2">表題:</label>
                    <input type="text" id="title" name="title" class="border border-gray-300 rounded-md w-full py-2 px-3">
                </div>
                <div>
                    <label for="content" class="block font-bold mb-2">内容:</label>
                    <textarea id="content" name="content" class="border border-gray-300 rounded-md w-full py-2 px-3" rows="4"></textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        保存する
                    </button>
                    <button type="reset" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded">
                        入力内容を消去
                    </button>
                </div>
            </form>
        </div>
        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-sm sm:text-base md:text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>
</body>
</html>