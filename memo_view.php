<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザーIDの確認
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin_or_editor = ($_SESSION['kanri_flg'] == 1 || $_SESSION['modify_flg'] == 1);

// 完了状態の更新
if (isset($_POST['toggle_complete'])) {
    $memo_id = $_POST['memo_id'];
    $stmt = $pdo->prepare("UPDATE memos SET is_completed = NOT is_completed WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $memo_id, ':user_id' => $user_id]);
    header("Location: memo_view.php");
    exit;
}

// メモの取得（自分のメモと共有されたメモ）
$stmt = $pdo->prepare("
    SELECT m.*, u.username as owner_name, 
    GROUP_CONCAT(su.username SEPARATOR ', ') as shared_with
    FROM memos m
    LEFT JOIN users u ON m.user_id = u.id
    LEFT JOIN memo_shares ms ON m.id = ms.memo_id
    LEFT JOIN users su ON ms.user_id = su.id
    WHERE m.user_id = :user_id OR ms.user_id = :user_id
    GROUP BY m.id
    ORDER BY m.is_completed, m.id DESC
");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
if($status == false) {
    sql_error($stmt);
} else {
    $memos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= h($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= h($_SESSION['error_message']) ?></span>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if ($is_admin_or_editor): ?>
            <div class="mb-4">
                <a href="memo_edit.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-block">
                    新規メモ作成
                </a>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">完了</th>
                        <th class="px-4 py-2">カテゴリー</th>
                        <th class="px-4 py-2">タイトル</th>
                        <th class="px-4 py-2">作成者</th>
                        <th class="px-4 py-2">共有先</th>
                        <?php if ($is_admin_or_editor): ?>
                            <th class="px-4 py-2">操作</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memos as $memo): ?>
                        <tr class="hover:bg-gray-100 <?= $memo['is_completed'] ? 'bg-green-100' : '' ?>">
                            <td class="border px-4 py-2">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="memo_id" value="<?= $memo['id'] ?>">
                                    <button type="submit" name="toggle_complete" class="<?= $memo['is_completed'] ? 'text-green-500' : 'text-gray-500' ?>">
                                        <?= $memo['is_completed'] ? '✓' : '○' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="border px-4 py-2"><?= h($memo['category']) ?></td>
                            <td class="border px-4 py-2"><?= h($memo['title']) ?></td>
                            <td class="border px-4 py-2"><?= h($memo['owner_name']) ?></td>
                            <td class="border px-4 py-2"><?= $memo['is_private'] ? '非共有' : h($memo['shared_with']) ?></td>
                            <?php if ($is_admin_or_editor && $memo['user_id'] == $user_id): ?>
                                <td class="border px-4 py-2">
                                    <a href="memo_edit.php?id=<?= $memo['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-2">編集</a>
                                    <a href="memo_delete.php?id=<?= $memo['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('本当に削除しますか？');">削除</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-sm sm:text-base md:text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>
</body>
</html>