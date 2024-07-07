<?php
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
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            max-height: 80vh;
            object-fit: contain;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            -webkit-user-select: none;
        }
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }
        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
    </style>
</head>
<body class="bg-gray-200" id="body">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-center">アルバム一覧</h1>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (!$album_id): ?>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($albums as $album): ?>
                    <div class="bg-gray-200 p-4 rounded-lg shadow">
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
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($photos as $index => $photo): ?>
                    <div class="bg-gray-200 p-4 rounded-lg shadow">
                        <img src="uploads/<?= h($photo['file_name']) ?>" alt="Photo" class="w-full h-40 object-cover mb-2 rounded cursor-pointer" onclick="openModal('uploads/<?= h($photo['file_name']) ?>', '<?= h($photo['comment']) ?>', '<?= h($photo['username']) ?>', <?= $index ?>)">
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

    </div>

    <!-- モーダル -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <a class="prev" onclick="changePhoto(-1)">&#10094;</a>
        <a class="next" onclick="changePhoto(1)">&#10095;</a>
        <img class="modal-content" id="modalImg">
        <div id="caption" class="mt-4 text-white text-center"></div>
    </div>

    <?php include 'footer_photo.php'; ?>

    <script>
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("modalImg");
        var captionText = document.getElementById("caption");
        var span = document.getElementsByClassName("close")[0];
        var currentPhotoIndex = 0;
        var photos = <?php echo json_encode($photos); ?>;

        function openModal(src, comment, username, index) {
            modal.style.display = "block";
            modalImg.src = src;
            captionText.innerHTML = comment + "<br>投稿者: " + username;
            currentPhotoIndex = index;
        }

        function changePhoto(direction) {
            currentPhotoIndex += direction;
            if (currentPhotoIndex >= photos.length) {
                currentPhotoIndex = 0;
            } else if (currentPhotoIndex < 0) {
                currentPhotoIndex = photos.length - 1;
            }
            var photo = photos[currentPhotoIndex];
            modalImg.src = "uploads/" + photo.file_name;
            captionText.innerHTML = photo.comment + "<br>投稿者: " + photo.username;
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // キーボードでの操作
        document.onkeydown = function(e) {
            if (modal.style.display === "block") {
                if (e.keyCode == 37) { // 左矢印キー
                    changePhoto(-1);
                } else if (e.keyCode == 39) { // 右矢印キー
                    changePhoto(1);
                } else if (e.keyCode == 27) { // ESCキー
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
