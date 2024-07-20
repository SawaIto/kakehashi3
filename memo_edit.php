<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("メモの編集権限がありません。");
}

$memo_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $content = preg_replace('/^\s*\n/', '', trim($_POST['content']));
    $content = preg_replace('/\r\n|\r|\n/', "\n", $content);
    $shared_with = isset($_POST['shared_with']) ? $_POST['shared_with'] : [];

    if (empty($category) || empty($content)) {
        $error = 'カテゴリー、内容を入力してください。';
    } else {
        // メモ更新
        $stmt = $pdo->prepare("UPDATE memos SET category = ?, content = ? WHERE id = ? AND group_id = ?");
        $status = $stmt->execute([$category, $content, $memo_id, $_SESSION['group_id']]);

        if ($status) {
            // 共有設定を一旦削除
            $stmt = $pdo->prepare("DELETE FROM memo_shares WHERE memo_id = ?");
            $stmt->execute([$memo_id]);

            // 新しい共有設定を追加
            foreach ($shared_with as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO memo_shares (memo_id, user_id) VALUES (?, ?)");
                $stmt->execute([$memo_id, $user_id]);
            }

            $_SESSION['success_message'] = 'メモが正常に更新されました。';
            redirect('memo_view.php');
        } else {
            $error = 'メモの更新に失敗しました。';
        }
    }
}

// メモ情報取得
$stmt = $pdo->prepare("SELECT * FROM memos WHERE id = ? AND group_id = ?");
$stmt->execute([$memo_id, $_SESSION['group_id']]);
$memo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$memo) {
    redirect('memo_view.php');
    exit("メモが見つかりません。");
}

// 共有情報取得
$stmt = $pdo->prepare("SELECT user_id FROM memo_shares WHERE memo_id = ?");
$stmt->execute([$memo_id]);
$shared_with = $stmt->fetchAll(PDO::FETCH_COLUMN);

// グループメンバー取得
$stmt = $pdo->prepare("SELECT u.id, u.username FROM users u
                      JOIN group_members gm ON u.id = gm.user_id
                      WHERE gm.group_id = ? AND u.id != ?");
$stmt->execute([$_SESSION['group_id'], $_SESSION['user_id']]);
$group_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メモ編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body class="bg-blue-50">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-2 bg-white rounded shadow-md max-w-2xl">
        <h1 class="text-3xl font-bold mb-6 text-center">メモ編集</h1>
        <?php if (isset($error)) : ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label for="category" class="block text-lg font-semibold">カテゴリー：</label>
                <select id="category" name="category" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="買い物" <?= ($memo['category'] == '買い物') ? 'selected' : '' ?>>買い物</option>
                    <option value="やること" <?= ($memo['category'] == 'やること') ? 'selected' : '' ?>>やること</option>
                    <option value="その他" <?= ($memo['category'] == 'その他') ? 'selected' : '' ?>>その他</option>
                </select>
            </div>
            <div>
                <label for="content" class="block text-lg font-semibold">内容：</label>
                <textarea id="content" name="content" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4"><?= h($memo['content']) ?></textarea>
            </div>
            <div>
                <p class="text-lg font-semibold">共有先：</p>
                <div class="space-y-2 text-sm sm:text-base">
                    <?php foreach ($group_members as $member) : ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="shared_with[]" value="<?= h($member['id']) ?>" <?= in_array($member['id'], $shared_with) ? 'checked' : '' ?> class="mr-2 rounded border-blue-300 text-blue-500 focus:ring-blue-200">
                            <span><?= h($member['username']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded text-lg text-center transition duration-300">更新</button>
        </form>

        <!-- ホーム,　メモ一覧に戻るボタン -->
         
        <div class="flex justify-end mt-4 space-x-4">
    <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-2 rounded text-center text-sm sm:text-base transition duration-300 w-1/4">
        ホーム
    </a>
    <a href="memo_view.php" class="bg-orange-300 hover:bg-orange-400 text-black font-bold py-2 px-2 rounded text-center text-sm sm:text-base transition duration-300 w-1/4">
        メモ一覧
    </a>
</div>
    </div>
</body>
<p class="copyright text-xs mt-5 text-gray-600 text-center pb-2">&copy; Kakehashi2024 All rights reserved.</p>

</html>