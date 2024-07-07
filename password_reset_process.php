<?php
include("funcs.php");

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lid = $_POST['lid'];
    $email = $_POST['email'];

    // データベース接続
    $pdo = db_conn();

    // ユーザー情報の確認
    $stmt = $pdo->prepare("SELECT * FROM users WHERE lid = :lid AND email = :email");
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // トークンの生成
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // トークンをデータベースに保存
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expires = :expires WHERE id = :id");
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':expires', $expires, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user['id'], PDO::PARAM_INT);
        $stmt->execute();

        // メール送信（実際のメール送信処理はここに実装）
        $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token;
        $to = $email;
        $subject = "パスワードリセットのご案内";
        $message = "以下のリンクからパスワードをリセットしてください。\n\n" . $reset_link . "\n\nこのリンクは1時間後に無効になります。";
        $headers = "From: noreply@yourwebsite.com";

        // mail($to, $subject, $message, $headers);  // 実際のメール送信はここでコメントアウトを解除

        $success_message = "パスワードリセットの手順をメールで送信しました。";
    } else {
        $error_message = "入力された情報と一致するユーザーが見つかりませんでした。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>パスワードリセット処理</title>
</head>
<body class="bg-gray-200 min-h-screen flex flex-col">
    <?php include 'header0.php'; ?>
    
    <main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">パスワードリセット処理</h2>
            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $success_message; ?></span>
                </div>
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