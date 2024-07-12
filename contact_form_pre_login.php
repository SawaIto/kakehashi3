<?php

require_once 'funcs.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pdo = db_conn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $email_confirm = $_POST['email_confirm'];
    $content = $_POST['content'];

    if ($email !== $email_confirm) {
        $error = 'メールアドレスが一致しません。';
    } else {
        try {
            // データベースに保存
            $stmt = $pdo->prepare("INSERT INTO inquiries (user_id, email, content) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $email, $content]);

            // メール送信
            $mail = new PHPMailer(true);

            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'あなたのGmailアドレス'; // TODO: 変更してください
            $mail->Password   = 'アプリパスワード'; // TODO: 変更してください
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('あなたのGmailアドレス', 'かけ橋管理者'); // TODO: 変更してください
            $mail->addAddress('sawa110291@gmail.com', '管理者');

            // Content
            $mail->isHTML(true);
            $mail->Subject = '新しいお問い合わせがあります（未ログインユーザー）';
            $mail->Body    = "新しいお問い合わせがありました。<br><br>"
                           . "ユーザーID または メールアドレス: {$user_id}<br>"
                           . "メールアドレス: {$email}<br>"
                           . "内容: <br>{$content}";

            $mail->send();
            $success = 'お問い合わせを受け付けました。確認メールが送信されました。';
        } catch (Exception $e) {
            $error = "エラーが発生しました。: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ - かけ橋</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body class="bg-gray-200">
    <?php include 'header.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">お問い合わせ</h1>

        <?php if ($error): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="text-green-500 mb-4 text-center"><?= h($success) ?></p>
        <?php else: ?>
            <p class="mb-4 text-center">登録済みのユーザーの方は<a href="login.php" class="text-blue-500 hover:underline">ログイン</a>してからお問い合わせください。</p>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="user_id" class="block text-lg font-semibold">ユーザーID または 登録メールアドレス：</label>
                    <input type="text" id="user_id" name="user_id" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="email" class="block text-lg font-semibold">連絡先メールアドレス：</label>
                    <input type="email" id="email" name="email" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="email_confirm" class="block text-lg font-semibold">連絡先メールアドレス（確認）：</label>
                    <input type="email" id="email_confirm" name="email_confirm" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="content" class="block text-lg font-semibold">お問い合わせ内容：</label>
                    <textarea id="content" name="content" rows="4" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 whitespace-pre-line"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">送信</button>
            </form>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-xl transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>