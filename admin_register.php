<?php
session_start();
require_once 'funcs.php';

// データベース接続
$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // まず、同じメールアドレスが既に登録されていないか確認
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check_stmt->execute([$email]);
        $user_exists = $check_stmt->fetchColumn();

        if ($user_exists) {
            $error = "このメールアドレスは既に登録されています。";
        } else {
            // ユーザー登録
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$username, $email, $password]);

            // 登録が成功したかどうかチェック
            if ($stmt->rowCount() > 0) {
                $user_id = $pdo->lastInsertId();

                // グループ作成
                $stmt = $pdo->prepare("INSERT INTO groups (admin_id, name) VALUES (?, ?)");
                $stmt->execute([$user_id, $username . "'s Group"]);
                $group_id = $pdo->lastInsertId();

                // グループメンバーに管理者を追加
                $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
                $stmt->execute([$group_id, $user_id]);

                $_SESSION['success_message'] = "管理者登録が完了しました。";
                header("Location: registration_success.php");
                exit();
            } else {
                $error = "管理者登録に失敗しました。";
            }
        }
    } catch (PDOException $e) {
        $error = "エラーが発生しました: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者登録</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">管理者登録</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4" id="adminRegisterForm" autocomplete="off">
            <div>
                <label for="username" class="block text-lg font-semibold">ユーザー名：</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border rounded" autocomplete="new-password">
            </div>
            <div>
                <label for="email" class="block text-lg font-semibold">メールアドレス：</label>
                <input type="email" id="email" name="email" required class="w-full p-2 border rounded" autocomplete="new-password">
            </div>
            <div>
                <label for="password" class="block text-lg font-semibold">パスワード：</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border rounded" autocomplete="new-password">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-lg">登録</button>
        </form>
    </div>
    <script>
    window.onload = function() {
        document.getElementById('adminRegisterForm').reset();
    }
    </script>
</body>
</html>