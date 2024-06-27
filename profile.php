<?php
session_start();
include("funcs.php");
sschk();

$pdo = db_conn();

// ユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false){
    sql_error($stmt);
}else{
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

// POST処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lid = $_POST['lid'];
    $lpw = $_POST['lpw'];
    $email = isset($_POST['email']) ? $_POST['email'] : $row['email'];

    // パスワードが入力されている場合のみハッシュ化
    if(!empty($lpw)){
        $hashed_password = password_hash($lpw, PASSWORD_DEFAULT);
    }else{
        $hashed_password = $row['lpw'];
    }

    $sql = "UPDATE users SET lid=:lid, lpw=:lpw, email=:email WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $status = $stmt->execute();

    if($status==false){
        sql_error($stmt);
    }else{
        redirect('profile.php?success=updated');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>プロフィール編集</title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6">プロフィール</h2>
            
            <?php if(isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">プロフィールが正常に更新されました。</span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php if($_SESSION['kanri_flg'] == 1): ?>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            メールアドレス
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="<?= h($row['email']) ?>" required>
                    </div>
                <?php endif; ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="lid">
                        ログインID
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="lid" type="text" name="lid" value="<?= h($row['lid']) ?>" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="lpw">
                        新しいパスワード（変更する場合のみ入力）
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="lpw" type="password" name="lpw">
                </div>
                <div class="mb-6">
                    <span class="block text-gray-700 text-sm font-bold mb-2">現在の権限</span>
                    <p>
                        <?php
                        $permissions = [];
                        if ($row['kanri_flg'] == 1) $permissions[] = '管理者';
                        if ($row['modify_flg'] == 1) $permissions[] = '編集者';
                        if ($row['view_flg'] == 1) $permissions[] = '閲覧者';
                        echo implode(', ', $permissions);
                        ?>
                    </p>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>