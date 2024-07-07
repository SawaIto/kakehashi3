<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("管理者またはmodify権限のユーザーのみがユーザー一覧を閲覧できます。");
}

$stmt = $pdo->prepare("SELECT u.id, u.username, u.email, u.role FROM users u
                      JOIN group_members gm ON u.id = gm.user_id
                      WHERE gm.group_id = ?");
$stmt->execute([$_SESSION['group_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 成功メッセージの表示
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// エラーメッセージの表示
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .content-wrapper {
            height: calc(100vh - 64px);
            /* ヘッダーの高さを引いた値 */
            overflow-y: auto;
        }

        .overflow-x-auto.schedule_table table td:first-of-type {
            width: 80px;
        }
    </style>
</head>

<body class="bg-gray-200">
    <?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-2 bg-white rounded-lg shadow-md max-w-4xl">
        <h1 class="text-3xl font-bold mb-6 text-center">ユーザー一覧</h1>

        <?php if (isset($success_message)) : ?>
            <p class="text-green-500 mb-4 text-center"><?= h($success_message) ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)) : ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error_message) ?></p>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full mb-6 bg-blue-50 border-collapse border border-blue-200">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="text-left sm:text-base text-xs font-semibold p-2 border border-blue-200">ユーザー名</th>
                        <th class="text-left sm:text-base text-xs font-semibold p-2 border border-blue-200">メールアドレス</th>
                        <th class="text-left sm:text-base text-xs font-semibold p-2 border border-blue-200">権限</th>
                        <?php if ($_SESSION['role'] == 'admin') : ?>
                            <th class="text-left sm:text-base text-xs font-semibold p-2 border border-blue-200 w-16 sm:w-24">操作</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr class="hover:bg-blue-100 transition-colors duration-200 td:first-of-type">
                            <td class="sm:text-base text-xs p-2 border border-blue-200"><?= h($user['username']) ?></td>
                            <td class="sm:text-base text-xs p-2 border border-blue-200"><?= h($user['email']) ?></td>
                            <td class="sm:text-base text-xs p-2 border border-blue-200"><?= h($user['role']) ?></td>
                            <?php if ($_SESSION['role'] == 'admin') : ?>
                                <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 extra-column">
                                    <div class="flex flex-col space-y-2">
                                        <a href="user_edit.php?id=<?= $user['id'] ?>" class="text-center py-1 px-2 bg-blue-500 text-white hover:bg-blue-700 rounded">編集</a>
                                        <a href="user_delete.php?id=<?= $user['id'] ?>" class="text-center py-1 px-2 bg-red-500 text-white hover:bg-red-700 rounded" onclick="return confirm('本当に削除しますか？');">削除</a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center space-x-4">
            <?php if ($_SESSION['role'] == 'admin') : ?>
                <a href="user_register.php" class="bg-blue-400 hover:bg-blue-500 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">
                    新規ユーザー追加
                </a>
            <?php endif; ?>
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base md:text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>
</body>

</html>