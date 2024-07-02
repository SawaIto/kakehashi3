<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin_or_editor = ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify');


// 検索とフィルタリング
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$importance = isset($_GET['importance']) ? $_GET['importance'] : '';

$query = "
    SELECT m.*, u.username as creator,
    GROUP_CONCAT(DISTINCT su.username SEPARATOR ', ') as shared_with
    FROM memos m
    JOIN users u ON m.user_id = u.id
    LEFT JOIN memo_shares ms ON m.id = ms.memo_id
    LEFT JOIN users su ON ms.user_id = su.id
    WHERE m.group_id = ? AND (m.user_id = ? OR ms.user_id = ?)
";

$params = [$_SESSION['group_id'], $user_id, $user_id];

if ($search) {
    $query .= " AND m.content LIKE ?";
    $params[] = "%$search%";
}

if ($category) {
    $query .= " AND m.category = ?";
    $params[] = $category;
}

if ($importance) {
    $query .= " AND m.importance = ?";
    $params[] = $importance;
}

$query .= " GROUP BY m.id ORDER BY m.due_date ASC, m.importance DESC, m.is_completed, m.id DESC";

$stmt = $pdo->prepare($query);
$status = $stmt->execute($params);

if ($status == false) {
    sql_error($stmt);
} else {
    $memos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = ['買い物', 'スケジュール', 'やること', 'その他'];
$importance_levels = ['低', '普通', '高'];
?>

<!DOCTYPE html>
<html lang="ja" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メモ一覧</title>
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

<body class="flex flex-col min-h-full bg-gray-100">
    <!-- <? include 'header_test.php'; ?> -->
    <main class="flex-grow">
        <div class="container mx-auto mt-5 p-2 bg-white rounded-lg shadow-md max-w-4xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">メモ一覧</h1>
                <!-- <div class="flex justify-end space-x-2 w-full sm:w-auto">
                    <?php if ($is_admin_or_editor) : ?>
                        <a href="memo_input.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                            新規登録
                        </a>
                    <?php endif; ?>
                    <button onclick="toggleSearchFilter()" class="bg-blue-400 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                        検索
                    </button>
                    <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                        詳細
                    </button>
                </div>
            </div> -->
            <!-- <?php if (isset($_SESSION['success_message'])) : ?>
                <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])) : ?>
                <p class="text-sm sm:text-base text-red-500 mb-4 text-center"><?= h($_SESSION['error_message']) ?></p>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?> -->

            <!-- 検索とフィルタリングフォーム -->
            <!-- <div id="searchFilter" class="hidden mb-4 bg-gray-100 p-4 rounded-lg">
                <form action="" method="GET" class="flex flex-col sm:flex-row sm:items-end space-y-2 sm:space-y-0 sm:space-x-4">
                    <div class="flex-grow">
                        <input type="text" name="search" placeholder="検索..." value="<?= h($search) ?>" class="w-full p-2 border rounded">
                    </div>
                    <div class="flex-grow">
                        <select name="category" class="w-full p-2 border rounded">
                            <option value="">カテゴリー選択</option>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="<?= h($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">検索</button>
                        <a href="memo_view.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">リセット</a>
                    </div>
                </form>-->
            </div> 
            <div class="overflow-x-auto"> 
                <table class="w-full mb-6 border-collapse border border-blue-200">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="text-left text-xs sm:text-base md:text-lg font-semibold p-2 border border-blue-200">区分</th>
                            <th class="text-left text-xs sm:text-base md:text-lg font-semibold p-2 border border-blue-200">内容</th>
                            <th class="text-left text-xs sm:text-base md:text-lg font-semibold p-2 border border-blue-200 extra-column hidden">作成</th>
                            <th class="text-left text-xs sm:text-base md:text-lg font-semibold p-2 border border-blue-200 extra-column hidden">共有</th>
                            <?php if ($is_admin_or_editor) : ?>
                                <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200 extra-column hidden">操作</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($memos) > 0) : ?>
                            <?php foreach ($memos as $memo) : ?>
                                <tr class="hover:bg-blue-100 transition-colors duration-200">
                                    <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['category']) ?></td>
                                    <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['content']) ?></td>
                                    <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 extra-column hidden"><?= h($memo['creator']) ?></td>
                                    <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 extra-column hidden"><?= h($memo['shared_with'] ?: '共有なし') ?></td>
                                    <?php if ($is_admin_or_editor) : ?>
                                        <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 extra-column hidden">
                                            <a href="memo_edit.php?id=<?= $memo['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-2">編集</a>
                                            <a href="memo_delete.php?id=<?= $memo['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('本当に削除しますか？');">削除</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="<?= $is_admin_or_editor ? 5 : 4 ?>" class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 text-center">メモがありません。</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </main>
</body>
<? include 'footer_memo.php'; ?>

</html>