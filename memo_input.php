<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("メモの登録権限がありません。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $content = $_POST['content'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;
    $importance = $_POST['importance'];
    $due_date = $_POST['due_date'] ?: null;
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];

    if (empty($category) || empty($content)) {
        $error = 'カテゴリー、内容を入力してください。';
    } else {
        // メモ登録
        $stmt = $pdo->prepare("INSERT INTO memos (group_id, user_id, category,  content, is_private, importance, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $status = $stmt->execute([$_SESSION['group_id'], $_SESSION['user_id'], $category, $content, $is_private, $importance, $due_date]);

        if ($status) {
            $memo_id = $pdo->lastInsertId();

            // 共有設定
            if (!$is_private) {
                foreach ($shared_with as $user_id) {
                    $stmt = $pdo->prepare("INSERT INTO memo_shares (memo_id, user_id) VALUES (?, ?)");
                    $stmt->execute([$memo_id, $user_id]);
                }
            }

            $_SESSION['success_message'] = 'メモが正常に登録されました。';
            redirect('memo_view.php');
        } else {
            $error = 'メモの登録に失敗しました。';
        }
    }
}

// グループメンバー取得
$stmt = $pdo->prepare("SELECT u.id, u.username FROM users u
                      JOIN group_members gm ON u.id = gm.user_id
                      WHERE gm.group_id = ? AND u.id != ?");
$stmt->execute([$_SESSION['group_id'], $_SESSION['user_id']]);
$group_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メモ登録</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100">
    <?include 'header_test.php';?>
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">メモ登録</h1>
        <?php if (isset($error)) : ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label for="category" class="block text-lg font-semibold">カテゴリー：</label>
                <select id="category" name="category" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="" disabled selected>選択してください</option>
                    <option value="買い物">買い物</option>
                    <option value="やること">やること</option>
                    <option value="その他">その他</option>
                </select>
            </div>
            <div>
                <label for="content" class="block text-lg font-semibold">内容：</label>
                <textarea id="content" name="content" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4"></textarea>
            </div>
            <div>
                <p class="text-lg font-semibold">共有先：</p>
                <div class="space-y-2">
                    <?php foreach ($group_members as $member) : ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="shared_with[]" value="<?= h($member['id']) ?>" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                            <span><?= h($member['username']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">登録</button>
        </form>

        <!-- ホームに戻るボタン -->
        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-xl transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPrivateCheckbox = document.querySelector('input[name="is_private"]');
            const shareOptions = document.getElementById('share_options');

            isPrivateCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    shareOptions.style.display = 'none';
                } else {
                    shareOptions.style.display = 'block';
                }
            });
        });
    </script>
</body>

</html>
