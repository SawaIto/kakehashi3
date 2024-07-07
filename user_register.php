<?php

require_once 'funcs.php';

// データベース接続
$pdo = db_conn();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("管理者のみがユーザーを登録できます。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // emailの処理
    $email = ($role === 'view' && empty($_POST['email'])) ? null : $_POST['email'];

    // ユーザー登録
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    // 登録が成功したかどうかチェック
    if ($stmt->rowCount() > 0) {
        $user_id = $pdo->lastInsertId();

        // グループメンバーに追加
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['group_id'], $user_id]);

        $success_message = "ユーザー登録が完了しました。";
    } else {
        $error_message = "ユーザー登録に失敗しました。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body class="bg-gray-200">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">ユーザー登録</h1>
        <?php if (isset($success_message)): ?>
            <p class="text-green-500 mb-4 text-center"><?= h($success_message) ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error_message) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-lg font-semibold">ユーザー名：</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label for="role" class="block text-lg font-semibold">権限：</label>
                <select id="role" name="role" required class="w-full p-2 border rounded" onchange="toggleEmailField()">
                    <option value="modify">編集者</option>
                    <option value="view">閲覧者</option>
                </select>
            </div>
            <div id="emailField">
                <label for="email" class="block text-lg font-semibold">メールアドレス(編集者は必須)：</label>
                <input type="email" id="email" name="email" class="w-full p-2 border rounded">
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
            <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">登録</button>
        </form>
        <div class="mt-6 text-center">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-xl transition duration-300">
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

        function toggleEmailField() {
            const role = document.getElementById('role').value;
            const emailField = document.getElementById('emailField');
            const emailInput = document.getElementById('email');

            if (role === 'view') {
                emailField.style.display = 'none';
                emailInput.removeAttribute('required');
            } else {
                emailField.style.display = 'block';
                emailInput.setAttribute('required', 'required');
            }
        }

        // 初期表示時にも実行
        toggleEmailField();
    </script>
</body>
</html>