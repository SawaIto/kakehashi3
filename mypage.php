<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

// ユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    redirect('login.php');
}

// POST処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // パスワードが入力されている場合のみハッシュ化
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashed_password = $user['password'];
    }

    $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $status = $stmt->execute([$username, $email, $hashed_password, $_SESSION['user_id']]);

    if ($status == false) {
        $error = "マイページの更新に失敗しました。";
    } else {
        $_SESSION['success_message'] = "マイページが正常に更新されました。";
        redirect('mypage.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body class="bg-gray-200" id="body">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 mb-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">マイページ編集</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <p class="text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-lg font-semibold">ユーザー名：</label>
                <input type="text" id="username" name="username" value="<?= h($user['username']) ?>" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label for="email" class="block text-lg font-semibold">メールアドレス：</label>
                <input type="email" id="email" name="email" value="<?= h($user['email']) ?>" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label for="password" class="block text-lg font-semibold">新しいパスワード（変更する場合のみ入力）：</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded pr-10">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility()">
                        <i id="passwordToggleIcon" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            <div>
                <p class="text-lg font-semibold">現在の権限：</p>
                <p><?= h($user['role']) ?></p>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-lg w-full">更新</button>
        </form>
        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ホームに戻る
            </a>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggleIcon.classList.remove('fa-eye');
                passwordToggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggleIcon.classList.remove('fa-eye-slash');
                passwordToggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
