<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin') {
    redirect('home.php');
    exit("管理者のみがユーザーを登録できます。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // パスワードの確認
    if ($password !== $confirm_password) {
        $error_message = "パスワードが一致しません。";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // emailの処理
        $email = ($role === 'view' && empty($_POST['email'])) ? null : $_POST['email'];

        // ユーザー登録
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $role]);

        // 登録が成功したかどうかチェック
        if ($stmt->rowCount() > 0) {
            $user_id = $pdo->lastInsertId();

            // グループメンバーに追加
            $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['group_id'], $user_id]);

            $_SESSION['success_message'] = "ユーザー登録が完了しました。";
            redirect('user_list.php');
            exit;
        } else {
            $error_message = "ユーザー登録に失敗しました。";
        }
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
<body class="bg-gray-200" id="body">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">ユーザー登録</h1>
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error_message) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4" id="registrationForm">
            <div>
                <label for="username" class="block text-lg font-semibold">ユーザー名：</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border rounded" value="<?= isset($username) ? h($username) : '' ?>">
            </div>
            <div>
                <label for="role" class="block text-lg font-semibold">権限：</label>
                <select id="role" name="role" required class="w-full p-2 border rounded" onchange="toggleEmailField()">
                    <option value="modify" <?= isset($role) && $role === 'modify' ? 'selected' : '' ?>>編集者</option>
                    <option value="view" <?= isset($role) && $role === 'view' ? 'selected' : '' ?>>閲覧者</option>
                </select>
            </div>
            <div id="emailField" style="<?= isset($role) && $role === 'view' ? 'display: none;' : '' ?>">
                <label for="email" class="block text-lg font-semibold">メールアドレス(編集者は必須)：</label>
                <input type="email" id="email" name="email" class="w-full p-2 border rounded" value="<?= isset($email) ? h($email) : '' ?>" <?= isset($role) && $role !== 'view' ? 'required' : '' ?>>
            </div>
            <div>
                <label for="password" class="block text-lg font-semibold">パスワード：</label>
                <input type="text" id="password" name="password" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label for="confirm_password" class="block text-lg font-semibold">パスワード(確認用)：</label>
                <input type="text" id="confirm_password" name="confirm_password" required class="w-full p-2 border rounded">
            </div>
            <div class="m-2 text-center space-x-4">
                <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-3 py-2 rounded-lg text-sm sm:text-base text-center transition duration-300">登録</button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <a href="user_list.php" class="bg-green-300 hover:bg-green-400 text-black font-bold py-2 px-3 rounded text-sm sm:text-base transition duration-300">
                ユーザー一覧
            </a>
        </div>
    </div>
    <p class="text-sm mt-2 text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>
    <script>
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

        // フォーム送信時のイベントハンドラ
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                event.preventDefault(); // フォームの送信を防止
                alert('パスワードが一致しません。');
            }
        });
    </script>
</body>
<p class="text-sm mt-2 text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>

</html>