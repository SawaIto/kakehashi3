<?php
include("funcs.php");
$config = include("config.php"); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // データベース接続
    $pdo = db_conn();

    // 管理者または編集者情報の確認
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND (role = 'admin' OR role = 'modify')");
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

        // メール送信
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $config['mail']['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['mail']['username'];
            $mail->Password   = $config['mail']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $config['mail']['port'];
            $mail->setFrom($config['mail']['from_address'], $config['mail']['from_name']);
            $mail->addAddress($email);
            $mail->isHTML(true);

            $reset_link = $config['app']['url'] . "/reset_password.php?token=" . $token;
            $mail->Subject = 'パスワードリセットのご案内';
            $mail->Body    = "以下のリンクからパスワードをリセットしてください。<br><br>" . $reset_link . "<br><br>このリンクは1時間後に無効になります。";
            $mail->send();
            $success_message = "パスワードリセットの手順をメールで送信しました。";
        } catch (Exception $e) {
            $error_message = "メールの送信に失敗しました。";
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    } else {
        $error_message = "入力されたメールアドレスが見つかりませんでした。";
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
    
    <main class="flex-grow container mx-auto px-4 py-8 mt-16 sm:mt-20 flex items-center justify-center">
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