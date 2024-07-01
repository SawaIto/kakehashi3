<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

$current_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
// $display_period = isset($_GET['period']) ? $_GET['period'] : 'future';

// 期間指定の処理
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$query = "SELECT s.id, s.date, s.content, u.username as creator,
          GROUP_CONCAT(DISTINCT su.username SEPARATOR ', ') as shared_with
          FROM schedules s
          JOIN users u ON s.user_id = u.id
          LEFT JOIN schedule_shares ss ON s.id = ss.schedule_id
          LEFT JOIN users su ON ss.user_id = su.id
          WHERE s.group_id = ? AND (s.user_id = ? OR ss.user_id = ?)";

// 期間指定をクエリに追加
$params = [$_SESSION['group_id'], $_SESSION['user_id'], $_SESSION['user_id']];
// if ($start_date && $end_date) {
//     $query .= " AND s.date BETWEEN ? AND ?";
//     array_push($params, $start_date, $end_date);
// } elseif ($display_period == 'future') {
//     $query .= " AND s.date >= ?";
//     array_push($params, $current_date);
// }
if ($start_date && $end_date) {
    $query .= " AND s.date BETWEEN ? AND ?";
    array_push($params, $start_date, $end_date);
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
    <script>
        function toggleSearchFilter() {
            var searchFilter = document.getElementById('searchFilter');
            searchFilter.classList.toggle('hidden');
        }

        function toggleExtraColumns() {
            var extraColumns = document.querySelectorAll('.extra-column');
            extraColumns.forEach(function(column) {
                column.classList.toggle('hidden');
            });
        }
    </script>
</head>

<body class="bg-blue-100">
    <? include 'header_test.php'; ?>
    <div class="container mx-auto mt-10 p-2 bg-white rounded-lg shadow-md max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-center">スケジュール表示</h1>
            <button onclick="toggleExtraColumns()" class="bg-blue-400 hover:bg-blue-500 text-black font-bold py-1 px-2 rounded-lg text-xs transition duration-300">
                詳細表示
            </button>
        </div>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <button onclick="toggleSearchFilter()" class="mb-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            検索フィルタを表示/非表示
        </button>

<!-- 期間指定フォーム -->
<div id="searchFilter" class="hidden">
    <form method="GET" action="" class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end">
            <div class="flex-grow mb-2 sm:mb-0 sm:mr-4">
                <div class="flex items-center">
                    <label for="start_date" class="w-15 text-sm font-medium text-gray-700">開始日:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= h($start_date) ?>" class="flex-grow p-2 border rounded">
                </div>
            </div>
            <div class="flex-grow mb-4 sm:mb-0 sm:mr-4">
                <div class="flex items-center">
                    <label for="end_date" class="w-15 text-sm font-medium text-gray-700">終了日:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= h($end_date) ?>" class="flex-grow p-2 border rounded">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-2">
            <div class="space-x-2">
                <button type="submit" class="bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">検索</button>
                <a href="<?= $_SERVER['PHP_SELF'] ?>" class="inline-block bg-gray-400 hover:bg-gray-500 text-black font-bold px-4 py-2 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">リセット</a>
            </div>
        </div>
    </form>
</div>
        <?php
        // スケジュールを年ごとにグループ化する
        $schedulesByYear = [];
        foreach ($schedules as $schedule) {
            $year = date('Y', strtotime($schedule['date']));
            $schedulesByYear[$year][] = $schedule;
        }

        // 曜日の配列（0: 日曜日, 1: 月曜日, ...)
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        // 各年の最長日付文字列を計算
        $maxDateLengths = [];
        foreach ($schedulesByYear as $year => $yearSchedules) {
            $maxLength = 0;
            foreach ($yearSchedules as $schedule) {
                $date = new DateTime($schedule['date']);
                $formattedDate = $date->format('n/j') . '(' . $weekdays[$date->format('w')] . ')';
                $maxLength = max($maxLength, mb_strlen($formattedDate));
            }
            $maxDateLengths[$year] = $maxLength;
        }
        ?>

<div class="overflow-x-auto">
    <?php foreach ($schedulesByYear as $year => $yearSchedules): ?>
        <h3 class="text-2xl font-bold mb-4 mt-8"><?= h($year) ?>年</h3>
        <table class="w-full mb-6 bg-blue-50 border-collapse border border-blue-200">
            <thead>
                <tr class="bg-blue-100">
                    <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 text-center" style="width: <?= $maxDateLengths[$year] * 0.75 ?>em;">日付</th>
                    <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 text-center">内容</th>
                    <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 extra-column hidden w-14 sm:w-20 md:w-24 text-center">作成</th>
                    <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 extra-column hidden w-16 sm:w-20 md:w-24 text-center">共有</th>
                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 extra-column hidden w-12 sm:w-20 md:w-24 text-center">操作</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($yearSchedules as $schedule) : ?>
                    <?php
                    $date = new DateTime($schedule['date']);
                    $formattedDate = $date->format('n/j') . '(' . $weekdays[$date->format('w')] . ')';
                    ?>
                    <tr class="hover:bg-blue-100 transition-colors duration-200">
                        <td class="text-sm sm:text-base md:text-lg p-1 border border-blue-200" style="width: <?= $maxDateLengths[$year] * 0.75 ?>em;"><?= h($formattedDate) ?></td>
                        <td class="text-sm sm:text-base md:text-lg p-1 border border-blue-200 whitespace-pre-wrap"><?= h($schedule['content']) ?></td>
                        <td class="text-sm sm:text-base md:text-lg p-1 border border-blue-200 extra-column hidden w-14 sm:w-20 md:w-24 text-center"><?= h($schedule['creator']) ?></td>
                        <td class="text-sm sm:text-base md:text-lg p-1 border border-blue-200 extra-column hidden w-16 sm:w-20 md:w-24 text-center"><?= h($schedule['shared_with'] ?: '共有なし') ?></td>
                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                            <td class="text-sm sm:text-base md:text-lg p-1 border border-blue-200 extra-column hidden w-12 sm:w-20 md:w-24">
                                <div class="flex flex-col sm:flex-row sm:space-x-2">
                                    <a href="schedule_edit.php?id=<?= h($schedule['id']) ?>" class="text-blue-500 hover:text-blue-700 text-center mb-1 sm:mb-0">編集</a>
                                    <a href="schedule_delete.php?id=<?= h($schedule['id']) ?>" class="text-red-500 hover:text-red-700 text-center" onclick="return confirm('本当に削除しますか？');">削除</a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>

        <!-- ホームに戻るボタンとスケジュール登録ボタン -->
        <div class="mt-6 text-center space-x-2">
            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
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