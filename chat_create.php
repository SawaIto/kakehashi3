<?php
require_once 'funcs.php';
sschk();
$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("予定の登録権限がありません。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    $chat_name = $_POST['chat_name'];
    $is_top_flag = isset($_POST['is_top_flag']) ? 1 : 0;
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];
    $chat_member = json_encode($shared_with);
    // echo "<pre>";
    // var_dump($chat_member);
    // echo "</pre>";

    if (empty($chat_name) || empty($chat_member)) {
        $error = 'チャット名とメンバーを入力してください。';
    } else {
        $stmt = $pdo->prepare("INSERT INTO chats (group_id, user_id, chat_member, chat_name, is_top_flag, created_at, updated_at, updated_by) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)");
        $status = $stmt->execute([$_SESSION['group_id'], $_SESSION['user_id'], $chat_member, $chat_name, $is_top_flag, $_SESSION['user_id']]);
    }
}

$stmt = $pdo->prepare("SELECT u.id, u.username FROM users u
                      JOIN group_members gm ON u.id = gm.user_id
                      WHERE gm.group_id = ?");
$stmt->execute([$_SESSION['group_id']]);
$group_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>予定登録</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="styles/main.css">
<style>
body {display: flex;flex-direction: column;min-height: 100vh;}
main {flex: 1;display: flex;flex-direction: column;padding-top: 64px;}
.content-wrapper {flex: 1;overflow-y: auto;}
</style>
</head>

<body class="bg-blue-50" id=body>
    <?php include 'header0.php'; ?>
    <main>
        <div class="content-wrapper">
            <div class="container mx-auto p-6">
                <div class="bg-white rounded shadow-md max-w-md mx-auto">
                    <h1 class="text-3xl font-bold mb-6 text-center pt-6">予定登録</h1>
                    <?php if (isset($error)) : ?>
                        <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4 p-6">
                        <?/*
                        <div>
                            <label for="date" class="block text-lg font-semibold">日付：</label>
                            <input type="date" id="date" name="date" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <p class="text-lg font-semibold">誰の予定：</p>
                            <div class="space-y-2 text-sm sm:text-base">
                                <?php foreach ($group_members as $member) : ?>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="schedule_for[]" value="<?= h($member['id']) ?>" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                                        <span><?= h($member['username']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                                <label class="flex items-center">
                                    <input type="checkbox" name="others_checkbox" id="others_checkbox" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                                    <span>その他</span>
                                </label>
                                <input type="text" name="others" id="others" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" style="display: none;">
                            </div>
                        </div>
                        */?>
                        <div>
                            <label for="content" class="block text-lg font-semibold">チャット名</label>
                            <!-- <textarea id="content" name="content" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="2"></textarea> -->
                            <input type="text" id="chat_name" name="chat_name" required class="w-full p-2 border rounded">
                        </div>
                        <div>
                            <p class="text-lg font-semibold">メンバー</p>
                            <div class="space-y-2 text-sm sm:text-base">
                                <?php foreach ($group_members as $member) : ?>
                                    <?php if ($member['id'] != $_SESSION['user_id']) : ?>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="shared_with[]" value="<?= h($member['id']) ?>" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                                            <span><?= h($member['username']) ?></span>
                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?if($_SESSION["role"] == "admin"){?>
                        <div class="">
                            <input type="checkbox" name="is_top_flag" id="is_top_flag" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                            <label for="is_top_flag">最上部に表示(ピン留め)</label>
                        </div>
                        <?}?>

                        <button type="submit" class="w-full bg-amber-700 hover:bg-amber-500 text-white font-bold px-4 py-2 rounded text-lg text-center transition duration-300">登録</button>
                        <div class="flex justify-center space-x-4 mb-4">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-sm sm:text-base transition duration-300">
                ホーム
            </a>
            <a href="schedule_view.php" class="bg-blue-400 hover:bg-blue-500 text-black font-bold py-2 px-4 rounded text-sm sm:text-base transition duration-300">
                予定表示
            </a>
            <br>
                                    </div>
                    </form>

        </div>
                </div>
            </div>
        </div>
    </main>
    <p class="text-sm m-3 text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>

    <!-- <?php include 'footer_schedule.php'; ?> -->
    <script>
        document.getElementById('others_checkbox').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('others').style.display = 'block';
            } else {
                document.getElementById('others').style.display = 'none';
            }
        });
    </script>
</body>

</html>
