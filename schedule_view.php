<?php

require_once 'funcs.php';
sschk();

$pdo = db_conn();

$current_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$query = "SELECT s.id, s.date, s.content, u.username as creator,
          GROUP_CONCAT(DISTINCT su.username SEPARATOR ', ') as shared_with
          FROM schedules s
          JOIN users u ON s.user_id = u.id
          LEFT JOIN schedule_shares ss ON s.id = ss.schedule_id
          LEFT JOIN users su ON ss.user_id = su.id
          WHERE s.group_id = ? AND (s.user_id = ? OR ss.user_id = ?)";

$params = [$_SESSION['group_id'], $_SESSION['user_id'], $_SESSION['user_id']];

if ($start_date && $end_date) {
    $query .= " AND s.date BETWEEN ? AND ?";
    array_push($params, $start_date, $end_date);
}

$query .= " GROUP BY s.id ORDER BY s.date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$schedulesByYear = [];
foreach ($schedules as $schedule) {
    $year = date('Y', strtotime($schedule['date']));
    $schedulesByYear[$year][] = $schedule;
}

$weekdays = ['日', '月', '火', '水', '木', '金', '土'];

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

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スケジュール表示</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
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
    <style>
        .content-wrapper {
            height: calc(100vh - 64px);
            overflow-y: auto;
        }

        .overflow-x-auto.schedule_table table td:first-of-type {
            width: 90px;
        }

        .meta-info {
            font-size: 0.5rem;
            color: #666;
        }
    </style>
</head>

<body class="bg-gray-200" id="body">
    <?php include 'header0.php'; ?>
    <div class="pt-16 sm:pt-20">
        <main class="container mx-auto mt-5 mb-40">
            <div class="bg-white rounded-lg shadow-md p-1 sm:p-6">
                <div class="text-center mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold">スケジュール表示</h1>
                </div>

                <div class="overflow-x-auto schedule_table">
                    <div class="inline-block w-full py-1 px-1 align-middle sm:px-6 lg:px-8">
                        <?php foreach ($schedulesByYear as $year => $yearSchedules) : ?>
                            <h3 class="text-xl sm:text-2xl font-bold mb-2 mt-4"><?= h($year) ?>年</h3>
                            <table class="min-w-full border-collapse border border-blue-200">
                                <thead>
                                    <tr class="bg-blue-100">
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 text-center">日付</th>
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 text-center">内容</th>
                                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                            <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 extra-column hidden text-center w-16 sm:w-24">操作</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($yearSchedules as $schedule) : ?>
                                        <?php
                                        $date = new DateTime($schedule['date']);
                                        $formattedDate = $date->format('n/j') . '(' . $weekdays[$date->format('w')] . ')';
                                        ?>
                                        <tr class="hover:bg-blue-50 transition-colors duration-200">
                                            <td class="text-xs sm:text-sm p-2 border border-blue-200"><?= h($formattedDate) ?></td>
                                            <td class="text-xs sm:text-sm p-2 border border-blue-200">
                                                <div class="whitespace-pre-wrap"><?= h($schedule['content']) ?></div>
                                                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                                <div class="meta-info extra-column hidden text-end">
                                                    作成: <?= h($schedule['creator']) ?><br>
                                                    共有: <?= h($schedule['shared_with'] ?: '共有なし') ?>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                                <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 extra-column hidden">
                                                    <div class="flex flex-col space-y-2">
                                                        <a href="schedule_edit.php?id=<?= $schedule['id'] ?>" class="text-center py-1 px-2 bg-blue-500 text-white hover:bg-blue-700 rounded">編集</a>
                                                        <a href="schedule_delete.php?id=<?= $schedule['id'] ?>" class="text-center py-1 px-2 bg-red-500 text-white hover:bg-red-700 rounded" onclick="return confirm('本当に削除しますか？');">削除</a>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include 'footer_schedule.php'; ?>
</body>

</html>
