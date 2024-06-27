<?php
session_start();
include("funcs.php");
sschk();

// 権限チェック
if ($_SESSION['kanri_flg'] != 1 && $_SESSION['modify_flg'] != 1) {
    header("Location: calendar_view.php");
    exit;
}

$pdo = db_conn();
$user_id = $_SESSION['user_id'];

// ユーザーリストの取得（共有機能用）
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE id != :current_user_id");
$stmt->bindValue(':current_user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カレンダー入力</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6 text-center">カレンダー入力</h2>
            <form id="eventForm" action="create_event.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="title">
                        タイトル:
                    </label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="title" type="text" name="title" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="description">
                        説明:
                    </label>
                    <textarea class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="description" name="description" rows="4"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="start_date">
                        開始日時:
                    </label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="start_date" type="text" name="start_date" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="end_date">
                        終了日時:
                    </label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="end_date" type="text" name="end_date" required>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_private" id="is_private" class="form-checkbox">
                        <span class="ml-2">プライベートイベント（共有しない）</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2">共有するユーザー:</label>
                    <?php foreach ($users as $user): ?>
                        <label class="inline-flex items-center mt-3">
                            <input type="checkbox" name="shared_users[]" value="<?= $user['id'] ?>" class="form-checkbox">
                            <span class="ml-2"><?= h($user['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center justify-between">
                    <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="保存">
                    <a href="calendar_view.php" class="text-blue-500 hover:text-blue-700">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>

    <script>
    flatpickr("#start_date", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        locale: "ja"
    });

    flatpickr("#end_date", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        locale: "ja"
    });
    </script>
</body>
</html>