<?php
include("funcs.php");

$error_message = "";
$success_message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $pdo = db_conn();

    // トークンの有効性を確認
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expires > NOW() AND (role = 'admin' OR role = 'modify')");
    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error_message = "無効または期限切れのトークンです。";
    }
} else {
    $error_message = "トークンが見つかりません。";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expires = NULL WHERE id = :id");
        $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['id'], PDO::PARAM_INT);
        $stmt->execute();

        $success_message = "パスワードが正常にリセットされました。";
    } else {
        $error_message = "パスワードが一致しません。";
    }
}

// HTML部分は既存のコードをそのまま使用
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css">
    <title>パスワードリセット</title>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col">
    <?php include 'header0.php'; ?>
    
    <main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">新しいパスワードの設定</h2>
            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $success_message; ?></span>
                </div>
            <?php else: ?>
                <?php if (!$error_message): ?>
                    <form action="reset_password.php?token=<?php echo $token; ?>" method="post">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-xl font-bold mb-2" for="new_password">新しいパスワード:</label>
                            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="new_password" type="password" name="new_password" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-xl font-bold mb-2" for="confirm_password">パスワードの確認:</label>
                            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="confirm_password" type="password" name="confirm_password" required>
                        </div>
                        <div class="flex items-center justify-between">
                            <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="パスワードを更新">
                        </div>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
            <div class="text-center mt-4">
                <a href="login.php" class="text-center font-bold hover:text-blue-700 text-lg">ログインページに戻る</a>
            </div>
        </div>
    </main>

    <footer class="w-full bg-white shadow-md mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>