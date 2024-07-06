<?php
session_start();
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
            /* ヘッダーの高さを引いた値 */
            overflow-y: auto;
        }

        .overflow-x-auto.schedule_table table td:first-of-type {
            width: 90px;
        }
    </style>
</head>

<?php include 'header0.php'; ?>

<body class="bg-gray-100">
    <div class="pt-16 sm:pt-20">
        <main class="container mx-auto mt-5 mb-40">
            <div class="bg-white rounded-lg shadow-md p-1 sm:p-6">
                <div class="text-center mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold">スケジュール表示</h1>
                </div>
                <!-- <div class="flex justify-end space-x-2 w-full sm:w-auto">
                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                        <a href="schedule_input.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 sm:px-4 rounded-lg text-sm sm:text-base transition duration-300">
                            新規登録
                        </a>
                    <?php endif; ?>
                    <button onclick="toggleSearchFilter()" class="bg-blue-400 hover:bg-blue-500 text-white font-bold py-2 px-3 sm:px-4 rounded-lg text-sm sm:text-base transition duration-300">
                        検索
                    </button>
                    <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-white font-bold py-2 px-3 sm:px-4 rounded-lg text-sm sm:text-base transition duration-300">
                        詳細表示
                    </button>
                </div>
            </div> -->

                <!-- <?php if (isset($_SESSION['success_message'])) : ?>
                <p class="text-green-500 mb-4 text-center text-sm sm:text-base"><?= h($_SESSION['success_message']) ?></p>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div id="searchFilter" class="hidden mb-4 bg-gray-100 p-4 rounded-lg">
                <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-end space-y-2 sm:space-y-0 sm:space-x-4">
                    <div class="flex-grow">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">開始日:</label>
                        <input type="date" id="start_date" name="start_date" value="<?= h($start_date) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div class="flex-grow">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">終了日:</label>
                        <input type="date" id="end_date" name="end_date" value="<?= h($end_date) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">検索</button>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">リセット</a>
                    </div>
                </form>
            </div> -->

                <div class="overflow-x-auto schedule_table">
                    <div class="inline-block w-full py-1 px-1 align-middle sm:px-6 lg:px-8">
                        <?php foreach ($schedulesByYear as $year => $yearSchedules) : ?>
                            <h3 class="text-xl sm:text-2xl font-bold mb-2 mt-4"><?= h($year) ?>年</h3>
                            <table class="min-w-full border-collapse border border-blue-200">
                                <thead>
                                    <tr class="bg-blue-100">
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 text-center">日付</th>
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 text-center">内容</th>
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 extra-column hidden text-center">作成</th>
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 extra-column hidden text-center">共有</th>
                                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                            <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 extra-column hidden text-center">操作</th>
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
                                            <td class="text-xs sm:text-sm p-2 border border-blue-200 whitespace-pre-wrap"><?= h($schedule['content']) ?></td>
                                            <td class="text-xs sm:text-sm p-2 border border-blue-200 extra-column hidden text-center"><?= h($schedule['creator']) ?></td>
                                            <td class="text-xs sm:text-sm p-2 border border-blue-200 extra-column hidden text-center"><?= h($schedule['shared_with'] ?: '共有なし') ?></td>
                                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                                <td class="text-xs sm:text-sm p-2 border border-blue-200 extra-column hidden">
                                                    <div class="flex justify-around">
                                                        <a href="schedule_edit.php?id=<?= h($schedule['id']) ?>" class="text-blue-500 hover:text-blue-700">編集</a>
                                                        <a href="schedule_delete.php?id=<?= h($schedule['id']) ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('本当に削除しますか？');">削除</a>
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
</body>
<?php include 'footer_schedule.php'; ?>

</html>