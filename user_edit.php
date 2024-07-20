<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin') {
    redirect('home.php');
    exit("管理者のみがユーザーを編集できます。");
}

$user_id = $_GET['id'];

// ユーザー情報の取得
$stmt = $pdo->prepare("SELECT u.*, gm.group_id FROM users u
                       JOIN group_members gm ON u.id = gm.user_id
                       WHERE u.id = ? AND gm.group_id = ?");
$stmt->execute([$user_id, $_SESSION['group_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = "指定されたユーザーが見つかりません。";
    redirect('user_list.php');
}

$is_admin = ($user['role'] == 'admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $is_admin ? 'admin' : $_POST['role'];

    // emailの処理
    $email = ($role === 'view' || empty($_POST['email'])) ? null : $_POST['email'];

    // パスワードが入力された場合のみ更新
    $password_sql = "";
    $params = [$username, $email, $role, $user_id];
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password = ?";
        $params = [$username, $email, $role, $password, $user_id];
    }

    $sql = "UPDATE users SET username = ?, email = ?, role = ?" . $password_sql . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $status = $stmt->execute($params);
        if ($status) {
            $_SESSION['success_message'] = "ユーザー情報が正常に更新されました。";
            redirect('user_list.php');
            exit;
        } else {
            $error = "ユーザー情報の更新に失敗しました。";
        }
    } catch (PDOException $e) {
        $error = "エラー: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body class="bg-blue-50" id="body">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded shadow-md max-w-2xl">
        <h1 class="text-4xl font-bold mb-6 text-center">ユーザー編集</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline text-base"><?= h($error) ?></span>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-6 bg-blue-50 p-6 rounded border border-blue-200">
            <div>
                <label for="username" class="block text-base font-medium text-gray-700 mb-1">ユーザー名</label>
                <input type="text" name="username" id="username" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white text-base" value="<?= h($user['username']) ?>">
            </div>
            <div>
                <label for="role" class="block text-base font-medium text-gray-700 mb-1">権限</label>
                <?php if ($is_admin): ?>
                    <input type="hidden" name="role" value="admin">
                    <p class="mt-1 text-base text-gray-700">管理者 (変更不可)</p>
                <?php else: ?>
                    <select name="role" id="role" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white text-base" onchange="toggleEmailField()">
                        <option value="modify" <?= $user['role'] == 'modify' ? 'selected' : '' ?>>編集者</option>
                        <option value="view" <?= $user['role'] == 'view' ? 'selected' : '' ?>>閲覧者</option>
                    </select>
                <?php endif; ?>
            </div>
            <div id="emailField">
                <label for="email" class="block text-base font-medium text-gray-700 mb-1">メールアドレス</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white text-base" value="<?= h($user['email']) ?>">
            </div>
            <div>
                <label for="password" class="block text-base font-medium text-gray-700 mb-1">新しいパスワード（変更する場合のみ）</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white text-base">
            </div>
            <div class="flex justify-center">
                <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded text-lg text-center transition duration-300">更新</button>
            </div>
        </form>
        <div class="mt-6 flex justify-center space-x-4">
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-sm sm:text-base transition duration-300">
                ホーム
            </a>
            <a href="user_list.php" class="bg-green-300 hover:bg-green-400 text-black font-bold py-2 px-4 rounded text-sm sm:text-base transition duration-300">
                ユーザー一覧
            </a>
        </div>
    </div>
    <script>
    function toggleEmailField() {
        const role = document.getElementById('role').value;
        const emailField = document.getElementById('emailField');
        const emailInput = document.getElementById('email');

        if (role === 'view') {
            emailField.style.display = 'none';
            emailInput.value = ''; // メールアドレスの値を空文字列にする
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