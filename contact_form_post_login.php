<?php
require_once 'funcs.php';
sschk();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pdo = db_conn();

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// ユーザー情報を取得
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    try {
        // データベースに保存
        $stmt = $pdo->prepare("INSERT INTO inquiries (user_id, email, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $user['email'], $content]);

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
        $mail->Subject = '新しいお問い合わせがあります（ログインユーザー）';
        $mail->Body    = "新しいお問い合わせがありました。<br><br>"
                       . "ユーザーID: {$user_id}<br>"
                       . "メールアドレス: {$user['email']}<br>"
                       . "ユーザーロール: {$user_role}<br>"
                       . "内容: <br>{$content}";

        $mail->send();
        $success = 'お問い合わせを受け付けました。確認メールが送信されました。';
    } catch (Exception $e) {
        $error = "エラーが発生しました。: {$mail->ErrorInfo}";
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
<body class="bg-blue-50">
    <?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">お問い合わせ</h1>

        <?php if ($error): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="text-green-500 mb-4 text-center"><?= h($success) ?></p>
        <?php else: ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="user_id" class="block text-lg font-semibold">ユーザーID：</label>
                    <input type="text" id="user_id" name="user_id" value="<?= h($user_id) ?>" readonly class="w-full p-2 border rounded-md border-blue-300 bg-gray-100">
                </div>

                <div>
                    <label for="email" class="block text-lg font-semibold">メールアドレス：</label>
                    <input type="email" id="email" name="email" value="<?= h($user['email']) ?>" readonly class="w-full p-2 border rounded-md border-blue-300 bg-gray-100">
                </div>

                <div>
                    <label for="content" class="block text-lg font-semibold">お問い合わせ内容：</label>
                    <textarea id="content" name="content" rows="4" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 whitespace-pre-line"></textarea>
                </div>

                <?php if ($user_role == 'view'): ?>
                    <p class="text-sm text-gray-600">※閲覧者の方へ：お問い合わせの回答は管理者が行います。</p>
                <?php endif; ?>

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