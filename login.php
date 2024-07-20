<?php
require_once 'funcs.php';

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

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
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 80px; /* ヘッダーの高さ分のパディング */
            padding-bottom: 60px; /* フッターの高さ分のパディング */
        }
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }
        .content-wrapper {
            width: 100%;
            max-width: 28rem;
            margin: 0 auto;
            padding: 1rem;
        }
        @media (max-width: 640px) {
            body {
                padding-top: 120px; /* スマートフォン用のヘッダーの高さ */
            }
        }
    </style>
</head>
<body class="bg-blue-50">
    <?php include 'header.php'; ?>
    <main>
        <div class="content-wrapper">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8">
                <h1 class="text-3xl font-bold mb-6 text-center">ログイン</h1>
                <?php if (isset($error)): ?>
                    <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
                <?php endif; ?>
                <form method="POST" class="space-y-4" id="loginForm" autocomplete="off">
                    <div>
                        <label for="username" class="block text-lg font-semibold">ユーザー名：</label>
                        <input type="text" id="username" name="username" required class="w-full p-2 border rounded" autocomplete="new-password">
                    </div>
                    <div>
                        <label for="password" class="block text-lg font-semibold">パスワード：</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required class="w-full p-2 border rounded pr-10" autocomplete="new-password">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility()">
                                <i id="passwordToggleIcon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-300 text-white font-bold px-4 py-3 rounded text-xl text-center transition duration-300">ログイン</button>
                </form>
                <div class="mt-6 text-center">
                    <a href="admin_register.php" class="block bg-amber-700 hover:bg-amber-500 text-white font-bold px-4 py-3 rounded text-xl text-center transition duration-300">管理者登録</a>
                </div>
                                    <!-- login.php の <form> タグの後に追加 -->
<div class="mt-4 text-end text-sm">
    <a href="password_reset.php" class="text-blue-500 hover:text-blue-700">パスワードを忘れた場合</a>
</div>
                <div class="mt-6 text-right">
                    <a href="login_auto_template.php" class="text-black font-bold text-base">special_login</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>

    <script>
    window.onload = function() {
        document.getElementById('loginForm').reset();
    }
    
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