<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('photo_view.php');
    exit;
}

$photo_id = $_GET['id'];

// 写真情報の取得
$stmt = $pdo->prepare("SELECT * FROM photos WHERE id = ? AND group_id = ?");
$stmt->execute([$photo_id, $_SESSION['group_id']]);
$photo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$photo) {
    redirect('photo_view.php');
    exit;
}

// タグの取得
$stmt = $pdo->prepare("SELECT tag FROM photo_tags WHERE photo_id = ?");
$stmt->execute([$photo_id]);
$tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = $_POST['comment'];
    $new_tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];

    // 写真情報の更新
    $stmt = $pdo->prepare("UPDATE photos SET comment = ? WHERE id = ?");
    $status = $stmt->execute([$comment, $photo_id]);

    if ($status) {
        // タグの更新
        $stmt = $pdo->prepare("DELETE FROM photo_tags WHERE photo_id = ?");
        $stmt->execute([$photo_id]);

        foreach ($new_tags as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                $stmt = $pdo->prepare("INSERT INTO photo_tags (photo_id, tag) VALUES (?, ?)");
                $stmt->execute([$photo_id, $tag]);
            }
        }

        $_SESSION['success_message'] = '写真情報が正常に更新されました。';
        redirect('photo_view.php');
    } else {
        $error = '写真情報の更新に失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>写真編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "メイリオ", Meiryo, sans-serif; }
    </style>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">写真編集</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <img src="uploads/<?= h($photo['file_name']) ?>" alt="Photo" class="w-full mb-4 rounded">
            </div>
            <div>
                <label for="comment" class="block text-lg font-semibold">コメント：</label>
                <textarea id="comment" name="comment" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4"><?= h($photo['comment']) ?></textarea>
            </div>
            <div>
                <label for="tags" class="block text-lg font-semibold">タグ：</label>
                <input type="text" id="tags" name="tags" value="<?= h(implode(',', $tags)) ?>" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="カンマ区切りで入力">
            </div>
            <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">更新</button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="photo_view.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-xl transition duration-300">
                写真一覧に戻る
            </a>
        </div>
    </div>
</body>
</html>