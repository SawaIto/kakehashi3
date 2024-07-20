<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("予定の編集権限がありません。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $date = $_POST['date'];
    $content = $_POST['content'];
    $schedule_for = isset($_POST['schedule_for']) ? $_POST['schedule_for'] : [];
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];
    $others = isset($_POST['others']) ? $_POST['others'] : '';

    if (empty($date) || empty($content)) {
        $error = '日付と予定を入力してください。';
    } else {
        $stmt = $pdo->prepare("UPDATE schedules SET date = ?, content = ?, others = ?, updated_at = NOW(), updated_by = ? WHERE id = ?");
        $status = $stmt->execute([$date, $content, $others, $_SESSION['user_id'], $id]);

        if ($status) {
            // 予定の対象者を更新
            $stmt = $pdo->prepare("DELETE FROM schedule_for WHERE schedule_id = ?");
            $stmt->execute([$id]);
            foreach ($schedule_for as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO schedule_for (schedule_id, user_id) VALUES (?, ?)");
                $stmt->execute([$id, $user_id]);
            }

            // 共有先を更新
            $stmt = $pdo->prepare("DELETE FROM schedule_shares WHERE schedule_id = ?");
            $stmt->execute([$id]);
            foreach ($shared_with as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO schedule_shares (schedule_id, user_id) VALUES (?, ?)");
                $stmt->execute([$id, $user_id]);
            }

            $_SESSION['schedule_message'] = '予定が正常に更新されました。';
            redirect('schedule_view.php');
            exit;
        } else {
            $error = '予定の更新に失敗しました。';
        }
    }
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT s.*, u.username as creator FROM schedules s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$stmt->execute([$id]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    redirect('schedule_view.php');
    exit("予定が見つかりません。");
}

$stmt = $pdo->prepare("SELECT u.id, u.username FROM users u
                      JOIN group_members gm ON u.id = gm.user_id
                      WHERE gm.group_id = ?");
$stmt->execute([$_SESSION['group_id']]);
$group_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT user_id FROM schedule_for WHERE schedule_id = ?");
$stmt->execute([$id]);
$schedule_for = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("SELECT user_id FROM schedule_shares WHERE schedule_id = ?");
$stmt->execute([$id]);
$shared_with = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予定編集</title>
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
            padding-bottom: 20px;
        }

        .content-wrapper {
            flex: 1;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-blue-50" id=body>
    <?php include 'header0.php'; ?>
    <main>
        <div class="content-wrapper">
            <div class="container mx-auto p-6">
                <div class="bg-white rounded shadow-md max-w-md mx-auto">
                    <h1 class="text-3xl font-bold mb-6 text-center pt-6">予定編集</h1>
                    <?php if (isset($error)) : ?>
                        <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4 p-6">
                        <input type="hidden" name="id" value="<?= h($schedule['id']) ?>">
                        <div>
                            <label for="date" class="block text-lg font-semibold">日付：</label>
                            <input type="date" id="date" name="date" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="<?= h($schedule['date']) ?>">
                        </div>
                        <div>
                            <p class="text-lg font-semibold">誰の予定：</p>
                            <div class="space-y-2 text-sm sm:text-base">
                                <?php foreach ($group_members as $member) : ?>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="schedule_for[]" value="<?= h($member['id']) ?>" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200" <?= in_array($member['id'], $schedule_for) ? 'checked' : '' ?>>
                                        <span><?= h($member['username']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                                <label class="flex items-center">
                                    <input type="checkbox" name="others_checkbox" id="others_checkbox" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200" <?= !empty($schedule['others']) ? 'checked' : '' ?>>
                                    <span>その他</span>
                                </label>
                                <input type="text" name="others" id="others" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="<?= h($schedule['others']) ?>" style="display: <?= !empty($schedule['others']) ? 'block' : 'none' ?>;">
                            </div>
                        </div>
                        <div>
                            <label for="content" class="block text-lg font-semibold">予定：</label>
                            <textarea id="content" name="content" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><?= h($schedule['content']) ?></textarea>
                        </div>
                        <div>
                            <p class="text-lg font-semibold">共有先：</p>
                            <div class="space-y-2 text-sm sm:text-base">
                                <?php foreach ($group_members as $member) : ?>
                                    <?php if ($member['id'] != $_SESSION['user_id']) : ?>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="shared_with[]" value="<?= h($member['id']) ?>" class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200" <?= in_array($member['id'], $shared_with) ? 'checked' : '' ?>>
                                            <span><?= h($member['username']) ?></span>
                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded text-lg text-center transition duration-300">更新</button>
  
                        <div class="flex justify-end mb-4 mt-10 space-x-2">
                    <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                                ホーム
                            </a>
                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                            <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-white font-bold py-2 px-4 sm:px-4 rounded text-xs sm:text-base transition duration-300">
                                詳細表示/非表示
                            </button>
                            <a href="schedule_input.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                                予定登録
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
        </div>
    </main>
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
<p class="text-sm text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>
</html>