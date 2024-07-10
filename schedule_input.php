<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("スケジュールの登録権限がありません。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $content = $_POST['content'];
    $schedule_for = isset($_POST['schedule_for']) ? $_POST['schedule_for'] : [];
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];
    $others = isset($_POST['others']) ? $_POST['others'] : '';

    if (empty($date) || empty($content)) {
        $error = '日付とスケジュールを入力してください。';
    } else {
        $stmt = $pdo->prepare("INSERT INTO schedules (group_id, user_id, date, content, others, created_at, updated_at, updated_by) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)");
        $status = $stmt->execute([$_SESSION['group_id'], $_SESSION['user_id'], $date, $content, $others, $_SESSION['user_id']]);

        if ($status) {
            $schedule_id = $pdo->lastInsertId();

            // スケジュールの対象者を保存
            foreach ($schedule_for as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO schedule_for (schedule_id, user_id) VALUES (?, ?)");
                $stmt->execute([$schedule_id, $user_id]);
            }

            // 共有先を保存
            foreach ($shared_with as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO schedule_shares (schedule_id, user_id) VALUES (?, ?)");
                $stmt->execute([$schedule_id, $user_id]);
            }

            $_SESSION['success_message'] = 'スケジュールが正常に登録されました。';
            redirect('schedule_view.php');
        } else {
            $error = 'スケジュールの登録に失敗しました。';
        }
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
    <title>スケジュール登録</title>
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
            padding-bottom: 120px;
        }

        .content-wrapper {
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-200" id=body>
    <?php include 'header0.php'; ?>
    <main>
        <div class="content-wrapper">
            <div class="container mx-auto p-6">
                <div class="bg-white rounded-lg shadow-md max-w-md mx-auto">
                    <h1 class="text-3xl font-bold mb-6 text-center pt-6">スケジュール登録</h1>
                    <?php if (isset($error)) : ?>
                        <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4 p-6">
                        <div>
                            <label for="date" class="block text-lg font-semibold">日付：</label>
                            <input type="date" id="date" name="date" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <p class="text-lg font-semibold">誰の予定：</p>
                            <div class="space-y-2">
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
                        <div>
                            <label for="content" class="block text-lg font-semibold">スケジュール：</label>
                            <textarea id="content" name="content" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                        </div>
                        <div>
                            <p class="text-lg font-semibold">共有先：</p>
                            <div class="space-y-2">
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
                        <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">登録</button>
                    </form>

                    <!-- ホームに戻るボタン -->
                    <div class="mt-6 text-center pb-6">
                        <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-xl transition duration-300">
                            ホームに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer_schedule.php'; ?>
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