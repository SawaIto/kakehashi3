<?php
require_once 'funcs.php';

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    if (strlen($_POST['password']) < 6) {
        $error = "パスワードは6文字以上で設定してください";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $check_stmt->execute([$email]);
            $user_exists = $check_stmt->fetchColumn();

            if ($user_exists) {
                $error = "このメールアドレスは既に登録されています。";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
                $stmt->execute([$username, $email, $password]);

                if ($stmt->rowCount() > 0) {
                    $user_id = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("INSERT INTO `groups` (user_id, name) VALUES (?, ?)");
                    $stmt->execute([$user_id, $username . "'s Group"]);
                    $group_id = $pdo->lastInsertId();

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
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者登録</title>
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
<body class="bg-gray-200">
    <?php include 'header.php'; ?>
    <main>
        <div class="content-wrapper">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-3xl font-bold mb-6 text-center">管理者登録</h1>
                <?php if (isset($error)): ?>
                    <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
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
                        <label for="password" class="block text-lg font-semibold">パスワード(6文字以上)：</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required class="w-full p-2 border rounded pr-10" autocomplete="new-password">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePasswordVisibility()">
                                <i id="passwordToggleIcon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-400 hover:bg-green-500 text-black font-bold px-4 py-3 rounded-lg text-base text-center transition duration-300">登録</button>
                </form>
                <div class="mt-6 text-center">
                    <a href="login.php" class="block bg-blue-500 hover:bg-blue-300 text-black font-bold px-4 py-3 rounded-lg text-xl text-center transition duration-300">ログイン</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>

    <script>
    window.onload = function() {
        document.getElementById('adminRegisterForm').reset();
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