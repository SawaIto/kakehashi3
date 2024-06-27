<?php
session_start();
include("funcs.php");

// POSTデータがある場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $lid = $_POST['lid'];
    $lpw = $_POST['lpw'];
    
    // パスワードのハッシュ化
    $hashed_password = password_hash($lpw, PASSWORD_DEFAULT);
    
    // データベース接続
    $pdo = db_conn();
    
    try {
        $pdo->beginTransaction();

        // グループの作成
        $sql = "INSERT INTO groups (name) VALUES (:group_name)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':group_name', $name . "'s Group", PDO::PARAM_STR);
        $stmt->execute();
        $group_id = $pdo->lastInsertId();

        // 管理者ユーザーの登録
        $sql = "INSERT INTO users (name, email, lid, lpw, kanri_flg, modify_flg, view_flg, group_id) VALUES (:name, :email, :lid, :lpw, 1, 1, 1, :group_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);
        $stmt->execute();

        $pdo->commit();
          if ($stmt->execute()) {
            $pdo->commit();
            redirect('login.php?registration=success');
        } else {
            $pdo->rollBack();
            $error_message = "登録中にエラーが発生しました。";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "登録中にエラーが発生しました：" . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>新規管理者登録</title>
</head>
<body class="bg-blue-100 min-h-screen">
    <?php include 'header0.php'; ?>
    
    <main class="flex-grow container mx-auto px-4 py-2">
        <div class="max-w-md mx-auto bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6 text-center">新規管理者登録</h2>
            <?php if(isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="name">名前:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="name" type="text" name="name" required placeholder="名前">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="email">メールアドレス:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="email" type="email" name="email" required placeholder="example@example.com">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="lid">ID:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="lid" type="text" name="lid" required placeholder="ユーザーID">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="lpw">パスワード:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="lpw" type="password" name="lpw" required placeholder="パスワード">
                </div>
                <div class="flex items-center justify-between">
                    <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="登録">
                </div>
            </form>
        </div>
    </main>

    <footer class="w-full bg-white shadow-md mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>