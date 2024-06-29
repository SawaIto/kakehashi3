<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$group_id = $_SESSION['group_id'];

// アルバム一覧の取得
$stmt = $pdo->prepare("SELECT * FROM albums WHERE group_id = ? ORDER BY created_at DESC");
$stmt->execute([$group_id]);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 特定のアルバムの写真を表示
$album_id = isset($_GET['id']) ? $_GET['id'] : null;
if ($album_id) {
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM photos p
                           JOIN album_photos ap ON p.id = ap.photo_id
                           JOIN users u ON p.user_id = u.id
                           WHERE ap.album_id = ?");
    $stmt->execute([$album_id]);
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->execute([$album_id]);
    $current_album = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 写真をアルバムから削除
if (isset($_POST['remove_from_album'])) {
    $photo_id = $_POST['photo_id'];
    $album_id = $_POST['album_id'];

    $stmt = $pdo->prepare("DELETE FROM album_photos WHERE album_id = ? AND photo_id = ?");
    $stmt->execute([$album_id, $photo_id]);

    $_SESSION['success_message'] = '写真がアルバムから削除されました。';
    redirect("album_view.php?id=$album_id");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アルバム一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "メイリオ", Meiryo, sans-serif; }
    </style>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-center">アルバム一覧</h1>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!$album_id): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($albums as $album): ?>
                    <div class="bg-gray-100 p-4 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-2"><?= h($album['name']) ?></h2>
                        <p class="text-sm text-gray-600 mb-2"><?= h($album['description']) ?></p>
                        <p class="text-xs text-gray-500"><?= h($album['created_at']) ?></p>
                        <a href="?id=<?= h($album['id']) ?>" class="mt-2 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">表示</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2 class="text-2xl font-bold mb-4"><?= h($current_album['name']) ?></h2>
            <p class="text-gray-600 mb-4"><?= h($current_album['description']) ?></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($photos as $photo): ?>
                    <div class="bg-gray-100 p-4 rounded-lg shadow">
                        <img src="uploads/<?= h($photo['file_name']) ?>" alt="Photo" class="w-full h-40 object-cover mb-2 rounded">
                        <p class="text-sm truncate"><?= h(substr($photo['comment'], 0, 50)) ?></p>
                        <p class="text-xs text-gray-500 mt-1">投稿者: <?= h($photo['username']) ?></p>
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="photo_id" value="<?= h($photo['id']) ?>">
                            <input type="hidden" name="album_id" value="<?= h($album_id) ?>">
                            <button type="submit" name="remove_from_album" class="text-red-500 hover:text-red-700" onclick="return confirm('本当にこの写真をアルバムから削除しますか？');">
                                アルバムから削除
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6">
                <a href="album_add_photos.php?id=<?= h($album_id) ?>" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-lg transition duration-300">
                    アルバムに写真を追加
                </a>
            </div>

            <div class="mt-6">
                <a href="album_view.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-lg transition duration-300">
                    アルバム一覧に戻る
                </a>
            </div>
        <?php endif; ?>

        <div class="mt-6 text-center space-x-2">
            <a href="photo_upload.php" class="bg-blue-400 hover:bg-blue-500 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300">
                新規写真アップロード
            </a>
            <a href="photo_view.php" class="bg-green-400 hover:bg-green-500 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300">
                写真一覧
            </a>
            <a href="home.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>
</body>
</html>