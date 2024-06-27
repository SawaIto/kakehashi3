<?php
session_start();
include "funcs.php";
sschk();

// 管理者以外はアクセス不可
if ($_SESSION["kanri_flg"] != 1) {
    redirect('select.php');
    exit;
}

$pdo = db_conn();

// SQLクエリの作成
$sql = "SELECT id, name, lid, kanri_flg, modify_flg, view_flg FROM users";

// SQLクエリの実行
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

// クエリ実行結果の処理
if ($status == false) {
    sql_error($stmt);
} else {
    $user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーリスト</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6">ユーザー一覧</h2>

            <?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] == 'updated'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">成功:</strong>
            <span class="block sm:inline">ユーザー情報が正常に更新されました。</span>
        </div>
    <?php elseif ($_GET['success'] == 'deleted'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">成功:</strong>
            <span class="block sm:inline">ユーザーが正常に削除されました。</span>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'self_delete'): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">エラー:</strong>
        <span class="block sm:inline">自分自身を削除することはできません。</span>
    </div>
<?php endif; ?>


            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-blue-200 border border-blue-300 rounded-md">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">名前</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LoginID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">権限</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-blue-200">
                        <?php if (!empty($user_list)) : ?>
                            <?php foreach ($user_list as $user) : ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= h($user['name']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= h($user['lid']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php
                                        $permissions = [];
                                        if ($user['kanri_flg'] == 1) $permissions[] = '管理者';
                                        if ($user['modify_flg'] == 1) $permissions[] = '編集';
                                        if ($user['view_flg'] == 1) $permissions[] = '閲覧';
                                        echo implode(', ', $permissions);
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="user_edit.php?id=<?= $user['id'] ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">編集</a>
                                        <a href="user_delete.php?id=<?= $user['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('本当に削除しますか？');">削除</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">ユーザーが存在しません。</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>