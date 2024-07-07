<?php
include("funcs.php");
sschk();
$pdo = db_conn();

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$group_id = $_SESSION['group_id'];
$album_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$album_id) {
    redirect('album_view.php');
    exit;
}

// アルバム情報の取得
$stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND group_id = ?");
$stmt->execute([$album_id, $group_id]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    redirect('album_view.php');
    exit;
}

// アルバムに含まれていない写真を取得
$stmt = $pdo->prepare("SELECT p.*, u.username FROM photos p
                       JOIN users u ON p.user_id = u.id
                       WHERE p.group_id = ? AND p.id NOT IN (
                           SELECT photo_id FROM album_photos WHERE album_id = ?
                       )");
$stmt->execute([$group_id, $album_id]);
$available_photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 写真をアルバムに追加
if (isset($_POST['add_to_album'])) {
    $photo_id = $_POST['photo_id'];

    $stmt = $pdo->prepare("INSERT INTO album_photos (album_id, photo_id) VALUES (?, ?)");
    $stmt->execute([$album_id, $photo_id]);

    $_SESSION['success_message'] = '写真がアルバムに追加されました。';
    redirect("album_add_photos.php?id=$album_id");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アルバムに写真を追加</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body class="bg-gray-200" id="body">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-center">アルバムに写真を追加</h1>
        <h2 class="text-2xl font-bold mb-4"><?= h($album['name']) ?></h2>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($available_photos as $photo): ?>
                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <img src="uploads/<?= h($photo['file_name']) ?>" alt="Photo" class="w-full h-40 object-cover mb-2 rounded">
                    <p class="text-sm truncate"><?= h(substr($photo['comment'], 0, 50)) ?></p>
                    <p class="text-xs text-gray-500 mt-1">投稿者: <?= h($photo['username']) ?></p>
                    <form method="POST" class="mt-2">
                        <input type="hidden" name="photo_id" value="<?= h($photo['id']) ?>">
                        <button type="submit" name="add_to_album" class="text-green-500 hover:text-green-700">
                            アルバムに追加
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6">
            <a href="album_view.php?id=<?= h($album_id) ?>" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-lg transition duration-300">
                アルバムに戻る
            </a>
        </div>
    </div>
</body>
<?php include 'footer_photo.php'; ?>
</html>
