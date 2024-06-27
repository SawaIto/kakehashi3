<?php
session_start();
include("funcs.php");
sschk();

// 管理者以外はアクセス不可
if ($_SESSION["kanri_flg"] != 1) {
    redirect('select.php');
    exit;
}

$pdo = db_conn();

// GETでIDを取得
$id = $_GET['id'];

// 自分自身を削除しようとしていないか確認
if ($id == $_SESSION['user_id']) {
    redirect('user_list.php?error=self_delete');
    exit;
}

// 削除の確認
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    // ユーザーを削除
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if($status==false) {
        sql_error($stmt);
    } else {
        redirect('user_list.php?success=deleted');
        exit;
    }
} else {
    // ユーザー情報を取得
    $stmt = $pdo->prepare("SELECT name, lid FROM users WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if($status==false) {
        sql_error($stmt);
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>ユーザー削除確認</title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6">ユーザー削除確認</h2>
            <p class="mb-4">以下のユーザーを削除しますか？</p>
            <p class="mb-4"><strong>名前：</strong> <?= h($row['name']) ?></p>
            <p class="mb-4"><strong>ログインID：</strong> <?= h($row['lid']) ?></p>
            <div class="flex items-center justify-between">
                <a href="user_delete.php?id=<?= $id ?>&confirm=yes" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    削除する
                </a>
                <a href="user_list.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    キャンセル
                </a>
            </div>
        </div>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>