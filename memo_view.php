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

// 完了状態の更新
if (isset($_POST['toggle_complete'])) {
    $memo_id = $_POST['memo_id'];
    $stmt = $pdo->prepare("UPDATE memos SET is_completed = NOT is_completed WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $memo_id, ':user_id' => $user_id]);
    header("Location: memo_view.php");
    exit;
}

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
    $query .= " AND (m.title LIKE ? OR m.content LIKE ?)";
    $params[] = "%$search%";
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
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メモ一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-4xl">
        <h1 class="text-3xl font-bold mb-6 text-center">メモ一覧</h1>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])) : ?>
            <p class="text-sm sm:text-base text-red-500 mb-4 text-center"><?= h($_SESSION['error_message']) ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- 検索とフィルタリングフォーム -->
        <form action="" method="GET" class="mb-4">
            <div class="flex flex-wrap -mx-2 mb-4">
                <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0">
                    <input type="text" name="search" placeholder="検索..." value="<?= h($search) ?>" class="w-full p-2 border rounded">
                </div>
                <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0">
                    <select name="category" class="w-full p-2 border rounded">
                        <option value="">カテゴリー選択</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= h($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0">
                    <select name="importance" class="w-full p-2 border rounded">
                        <option value="">重要度選択</option>
                        <?php foreach ($importance_levels as $index => $level) : ?>
                            <option value="<?= $index + 1 ?>" <?= $importance == $index + 1 ? 'selected' : '' ?>><?= h($level) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="w-full md:w-1/4 px-2">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">検索・フィルタ</button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full mb-6 bg-blue-50 border-collapse border border-blue-200">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">状態</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">カテゴリー</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">内容</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">重要度</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">期限</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">作成者</th>
                        <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">共有先</th>
                        <?php if ($is_admin_or_editor) : ?>
                            <th class="text-left text-sm sm:text-base md:text-lg font-semibold p-2 border border-blue-200">操作</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memos as $memo) : ?>
                        <tr class="hover:bg-blue-100 transition-colors duration-200">
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="memo_id" value="<?= $memo['id'] ?>">
                                    <button type="submit" name="toggle_complete" class="bg-blue-400 hover:bg-blue-500 text-black font-bold px-2 py-1 rounded-lg text-sm transition duration-300">
                                        <?= $memo['is_completed'] ? '完了済み' : '完了した' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['category']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['content']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($importance_levels[$memo['importance'] - 1]) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['due_date']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['creator']) ?></td>
                            <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200"><?= h($memo['shared_with'] ?: '共有なし') ?></td>
                            <?php if ($is_admin_or_editor) : ?>
                                <td class="text-sm sm:text-base md:text-lg p-2 border border-blue-200">
                                    <a href="memo_edit.php?id=<?= $memo['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-2">編集</a>
                                    <a href="memo_delete.php?id=<?= $memo['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('本当に削除しますか？');">削除</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center space-x-2">
            <?php if ($is_admin_or_editor) : ?>
                <a href="memo_input.php" class="bg-blue-400 hover:bg-blue-500 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">
                    新規メモ作成
                </a>
            <?php endif; ?>
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>
</body>

</html>