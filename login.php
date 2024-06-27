<?php
require_once 'funcs.php';

// データベース接続
$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザー認証
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // ログイン成功
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // グループIDを取得
        $stmt = $pdo->prepare("SELECT group_id FROM group_members WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['group_id'] = $group['group_id'];

        $_SESSION['chk_ssid'] = session_id();
        redirect('home.php');
    } else {
        $error = "ユーザー名またはパスワードが正しくありません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">ログイン</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-lg font-semibold">ユーザー名：</label>
                <input type="username" id="username" name="username" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label for="password" class="block text-lg font-semibold">パスワード：</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full p-2 border rounded pr-10">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility()">
                        <i id="passwordToggleIcon" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="block bg-blue-500 hover:bg-blue-300 text-black font-bold px-4 py-3 rounded-lg text-xl text-center transition duration-300 w-full">ログイン</button>
        </form>
        <div class="mt-6 text-center space-x-4">
        <a href="admin_register.php" class="block bg-green-500 hover:bg-green-300 text-black font-bold px-4 py-3 rounded-lg text-xl text-center transition duration-300">管理者登録</a>
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