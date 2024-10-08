<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("メモの登録権限がありません。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $content = preg_replace('/^\s*\n/', '', trim($_POST['content']));
    $content = preg_replace('/\r\n|\r|\n/', "\n", $content);
    $importance = $_POST['importance'];
    $due_date = $_POST['due_date'] ?: null;
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];

    if (empty($category) || empty($content)) {
        $error = 'カテゴリー、内容を入力してください。';
    } else {
        $stmt = $pdo->prepare("INSERT INTO memos (group_id, user_id, category, content, is_private, importance, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $status = $stmt->execute([$_SESSION['group_id'], $_SESSION['user_id'], $category, $content, $is_private, $importance, $due_date]);
        if ($status) {
            $memo_id = $pdo->lastInsertId();
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
    <link rel="stylesheet" href="styles/main.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-top: 64px;
            padding-bottom: 100px;
        }

        .content-wrapper {
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-blue-50">
    <?php include 'header0.php'; ?>
    <main>
        <div class="content-wrapper">
            <div class="container mx-auto p-6">
                <div class="bg-white rounded shadow-md max-w-md mx-auto">
                    <h1 class="text-3xl font-bold mb-6 text-center pt-6">メモ登録</h1>
                    <?php if (isset($error)) : ?>
                        <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4 p-6">
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
                            <textarea id="content" name="content" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 whitespace-pre-line" rows="4"></textarea>
                        </div>
                        <div>
                            <p class="text-lg font-semibold">共有先：</p>
                            <div class="space-y-2 text-sm sm:text-base">
                                <?php foreach ($group_members as $member) : ?>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="shared_with[]" value="<?= h($member['id']) ?>" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                                        <span><?= h($member['username']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-amber-700 hover:bg-amber-500 text-white font-bold px-4 py-2 rounded text-lg text-center transition duration-300">登録</button>
                        <div class="flex justify-end mb-4 mt-10 space-x-2">
                            <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-black font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                                ホーム
                            </a>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                <!-- <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-black font-bold py-2 px-2 rounded text-xs sm:text-base transition duration-300">
                                    詳細
                                </button> -->
                                <a href="memo_view.php" class="bg-orange-500 hover:bg-orange-600 text-black font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                                    メモ一覧
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($_SESSION['schedule_message'])) : ?>
                            <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['schedule_message']) ?></p>
                            <?php unset($_SESSION['schedule_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_message'])) : ?>
                            <p class="text-sm sm:text-base text-red-500 mb-4 text-center"><?= h($_SESSION['error_message']) ?></p>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                </div>

                </form>
            </div>
        </div>
        </div>
    </main>

    <!-- <?php include 'footer_memo.php'; ?> -->

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