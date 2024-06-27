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

// POST処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $lid = $_POST['lid'];
    $kanri_flg = isset($_POST['kanri_flg']) ? 1 : 0;
    $modify_flg = isset($_POST['modify_flg']) ? 1 : 0;
    $view_flg = isset($_POST['view_flg']) ? 1 : 0;

    $sql = "UPDATE users SET name=:name, lid=:lid, kanri_flg=:kanri_flg, modify_flg=:modify_flg, view_flg=:view_flg WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
    $stmt->bindValue(':modify_flg', $modify_flg, PDO::PARAM_INT);
    $stmt->bindValue(':view_flg', $view_flg, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if($status==false) {
        sql_error($stmt);
    } else {
        redirect('user_list.php?success=updated');
        exit;
    }
}

// ユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false) {
    sql_error($stmt);
} else {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>ユーザー編集</title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6">ユーザー編集</h2>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    名前
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="<?= h($row['name']) ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lid">
                    ログインID
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="lid" type="text" name="lid" value="<?= h($row['lid']) ?>" required>
            </div>
            <div class="mb-4">
                <span class="block text-gray-700 text-sm font-bold mb-2">権限</span>
                <label class="inline-flex items-center mt-3">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" name="kanri_flg" value="1" <?= $row['kanri_flg'] ? 'checked' : '' ?>><span class="ml-2 text-gray-700">管理者権限</span>
                </label>
                <label class="inline-flex items-center mt-3">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" name="modify_flg" value="1" <?= $row['modify_flg'] ? 'checked' : '' ?>><span class="ml-2 text-gray-700">編集権限</span>
                </label>
                <label class="inline-flex items-center mt-3">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" name="view_flg" value="1" <?= $row['view_flg'] ? 'checked' : '' ?>><span class="ml-2 text-gray-700">閲覧権限</span>
                </label>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    更新
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="user_list.php">
                    キャンセル
                </a>
            </div>
        </form>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>