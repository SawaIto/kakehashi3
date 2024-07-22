<?php
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

$query .= " GROUP BY m.id ORDER BY m.due_date ASC, m.is_completed, m.id DESC";

$stmt = $pdo->prepare($query);
$status = $stmt->execute($params);

if ($status == false) {
    sql_error($stmt);
} else {
    $memos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$categories = ['買い物', 'やること', 'その他'];
?>

<!DOCTYPE html>
<html lang="ja" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メモ一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1 0 auto;
            padding-top: 64px;
            padding-bottom: 64px;
        }

        .content-wrapper {
            max-height: calc(100vh - 128px);
            overflow-y: auto;
        }

        .overflow-x-auto.memo_table table td:first-of-type {
            width: 60px;
        }

        .meta-info {
            font-size: 0.5rem;
            color: #666;
        }

        .category-column {
            width: 90px;
            min-width: 90px;
            max-width: 90px;
        }
    </style>
    <script>
        function toggleExtraColumns() {
            var extraColumns = document.querySelectorAll('.extra-column');
            var isHidden = extraColumns[0].classList.contains('hidden');
            extraColumns.forEach(function(column) {
                column.classList.toggle('hidden');
            });
            // 状態をローカルストレージに保存
            localStorage.setItem('extraColumnsVisible', isHidden ? 'true' : 'false');
        }

        // ページ読み込み時に実行する関数
        function loadExtraColumnsState() {
            var isVisible = localStorage.getItem('extraColumnsVisible') === 'true';
            var extraColumns = document.querySelectorAll('.extra-column');
            extraColumns.forEach(function(column) {
                if (isVisible) {
                    column.classList.remove('hidden');
                } else {
                    column.classList.add('hidden');
                }
            });
        }

        // ページ読み込み時に状態を復元
        document.addEventListener('DOMContentLoaded', loadExtraColumnsState);
    </script>
</head>

<body class="bg-blue-50" id="body">
    <?php include 'header0.php'; ?>
    <main class="flex-grow mb-40">
        <div class="container mx-auto mt-20 p-2 bg-white rounded shadow-md max-w-4xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">メモ一覧</h1>

            </div>

            <div class="overflow-x-auto memo_table">
                <table class="min-w-full border-collapse border border-blue-200">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 category-column">区分</th>
                            <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200">内容</th>
                            <?php if ($is_admin_or_editor) : ?>
                                <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 text-center w-16 extra-column hidden">操作</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($memos) > 0) : ?>
                            <?php foreach ($memos as $memo) : ?>
                                <tr class="hover:bg-blue-50 transition-colors duration-200">
                                    <td class="text-xs sm:text-sm p-2 border border-blue-200 category-column"><?= h($memo['category']) ?></td>
                                    <td class="text-xs sm:text-sm p-2 border border-blue-200">
                                        <div class="whitespace-pre-wrap"><?= rtrim(h($memo['content'])) ?></div>
                                        <?php if ($is_admin_or_editor) : ?>
                                            <div class="meta-info extra-column hidden text-end mt-2">
                                                作成: <?= h($memo['creator']) ?><br>
                                                共有: <?= h($memo['shared_with'] ?: '共有なし') ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <?php if ($is_admin_or_editor) : ?>
                                        <td class="text-xs sm:text-sm p-2 border border-blue-200 extra-column hidden">
                                            <div class="flex flex-col space-y-2">
                                                <a href="memo_edit.php?id=<?= $memo['id'] ?>" class="text-center py-1 px-2 bg-blue-500 text-white hover:bg-blue-700 rounded" onclick="localStorage.setItem('extraColumnsVisible', 'true'); return true;">編集</a>
                                                <a href="memo_delete.php?id=<?= $memo['id'] ?>" class="text-center py-1 px-2 bg-red-500 text-white hover:bg-red-700 rounded" onclick="return confirm('本当に削除しますか？') && localStorage.setItem('extraColumnsVisible', 'true');">削除</a>
                                            </div>
                                        </td>
                                    <?php endif; ?>

                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="<?= $is_admin_or_editor ? 3 : 2 ?>" class="text-xs sm:text-sm p-2 border border-blue-200 text-center">メモがありません。</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="flex justify-end mb-4 mt-10 space-x-2">
                            <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-black font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                                ホーム
                            </a>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-black font-bold py-2 px-2 rounded text-xs sm:text-base transition duration-300">
                                詳細表示/非表示
                                </button>
                                <a href="memo_input.php" class="bg-orange-500 hover:bg-orange-600 text-black font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                                    メモ登録
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
            </div>
        </div>
    </main>
    <!-- <?php include 'footer_memo.php'; ?> -->
</body>

</html>