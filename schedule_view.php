<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

$current_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$display_period = isset($_GET['period']) ? $_GET['period'] : 'future';

$query = "SELECT s.id, s.date, s.content, u.username as creator,
          GROUP_CONCAT(DISTINCT su.username SEPARATOR ', ') as shared_with
          FROM schedules s
          JOIN users u ON s.user_id = u.id
          LEFT JOIN schedule_shares ss ON s.id = ss.schedule_id
          LEFT JOIN users su ON ss.user_id = su.id
          WHERE s.group_id = ? AND (s.user_id = ? OR ss.user_id = ?)";

if ($display_period == 'future') {
    $query .= " AND s.date >= ?";
    $params = [$_SESSION['group_id'], $_SESSION['user_id'], $_SESSION['user_id'], $current_date];
} else {
    $params = [$_SESSION['group_id'], $_SESSION['user_id'], $_SESSION['user_id']];
}

$query .= " GROUP BY s.id ORDER BY s.date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スケジュール表示</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-4xl">
        <h1 class="text-3xl font-bold mb-6 text-center">スケジュール表示</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="mb-4 text-center space-x-2">
            <a href="?period=future" class="bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">今後のスケジュール</a>
            <a href="?period=all" class="bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">すべてのスケジュール</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full mb-6 bg-blue-50 border-collapse border border-blue-200">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 whitespace-nowrap">日付</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">内容</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">作成者</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">共有先</th>
                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify'): ?>
                            <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">操作</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr class="hover:bg-blue-100 transition-colors duration-200">
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200 whitespace-nowrap"><?= h($schedule['date']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200 whitespace-pre-wrap"><?= h($schedule['content']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($schedule['creator']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($schedule['shared_with'] ?: '共有なし') ?></td>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify'): ?>
                                <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200">
                                    <a href="schedule_edit.php?id=<?= h($schedule['id']) ?>" class="text-blue-500 hover:text-blue-700">編集</a>
                                    <a href="schedule_delete.php?id=<?= h($schedule['id']) ?>" class="text-red-500 hover:text-red-700 ml-2" onclick="return confirm('本当に削除しますか？');">削除</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- ホームに戻るボタンとスケジュール登録ボタン -->
        <div class="mt-6 text-center space-x-2">
            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify'): ?>
                <a href="schedule_input.php" class="bg-blue-400 hover:bg-blue-500 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">
                    スケジュール登録
                </a>
            <?php endif; ?>
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>
</body>
</html>