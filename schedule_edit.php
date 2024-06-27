<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("スケジュールの編集権限がありません。");
}

$schedule_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $content = $_POST['content'];
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];

    if (empty($date) || empty($content)) {
        $error = '日付と内容を入力してください。';
    } else {
        // スケジュール更新
        $stmt = $pdo->prepare("UPDATE schedules SET date = ?, content = ? WHERE id = ? AND group_id = ?");
        $status = $stmt->execute([$date, $content, $schedule_id, $_SESSION['group_id']]);

        if ($status) {
            // 共有設定を一旦削除
            $stmt = $pdo->prepare("DELETE FROM schedule_shares WHERE schedule_id = ?");
            $stmt->execute([$schedule_id]);

            // 新しい共有設定を追加
            foreach ($shared_with as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO schedule_shares (schedule_id, user_id) VALUES (?, ?)");
                $stmt->execute([$schedule_id, $user_id]);
            }

            $_SESSION['success_message'] = 'スケジュールが正常に更新されました。';
            redirect('schedule_view.php');
        } else {
            $error = 'スケジュールの更新に失敗しました。';
        }
    }
}

// スケジュール情報取得
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? AND group_id = ?");
$stmt->execute([$schedule_id, $_SESSION['group_id']]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    redirect('schedule_view.php');
    exit("スケジュールが見つかりません。");
}

// 共有情報取得
$stmt = $pdo->prepare("SELECT user_id FROM schedule_shares WHERE schedule_id = ?");
$stmt->execute([$schedule_id]);
$shared_with = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
    <title>スケジュール編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">スケジュール編集</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label for="date" class="block text-lg font-semibold">日付：</label>
                <input type="date" id="date" name="date" value="<?= h($schedule['date']) ?>" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label for="content" class="block text-lg font-semibold">内容：</label>
                <textarea id="content" name="content" required class="w-full p-2 border rounded"><?= h($schedule['content']) ?></textarea>
            </div>
            <div>
                <p class="text-lg font-semibold">共有先：</p>
                <?php foreach ($group_members as $member): ?>
                    <label class="block">
                        <input type="checkbox" name="shared_with[]" value="<?= h($member['id']) ?>" <?= in_array($member['id'], $shared_with) ? 'checked' : '' ?>>
                        <?= h($member['username']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-lg">更新</button>
        </form>
        
        <!-- ホームに戻るボタン -->
        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ホームに戻る
            </a>
        </div>
    </div>
</body>
</html>