<?php
include("funcs.php");
sschk();
$pdo = db_conn();

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$group_id = $_SESSION['group_id'];

// アルバム一覧の取得（サムネイル付き）
$stmt = $pdo->prepare("
    SELECT a.*, p.file_name AS thumbnail
    FROM albums a
    LEFT JOIN (
        SELECT ap.album_id, p.file_name,
               p.upload_date
        FROM album_photos ap
        JOIN photos p ON ap.photo_id = p.id
    ) p ON a.id = p.album_id
    WHERE a.group_id = ?
    GROUP BY a.id
    ORDER BY a.created_at DESC
");
$stmt->execute(array($group_id));
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 特定のアルバムの写真を表示
$album_id = isset($_GET['id']) ? $_GET['id'] : null;
if ($album_id) {
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM photos p
                           JOIN album_photos ap ON p.id = ap.photo_id
                           JOIN users u ON p.user_id = u.id
                           WHERE ap.album_id = ?");
    $stmt->execute(array($album_id));
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->execute(array($album_id));
    $current_album = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 写真をアルバムから削除
if (isset($_POST['remove_from_album'])) {
    $photo_id = $_POST['photo_id'];
    $album_id = $_POST['album_id'];

    $stmt = $pdo->prepare("DELETE FROM album_photos WHERE album_id = ? AND photo_id = ?");
    $stmt->execute(array($album_id, $photo_id));

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
    <div class="container mx-auto mt-20 mb-40 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-center">アルバム一覧</h1>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-green-500 mb-4 text-center"><?php echo h($_SESSION['success_message']); ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])) : ?>
            <p class="text-red-500 mb-4 text-center"><?php echo h($_SESSION['error_message']); ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <?php if (!$album_id): ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php foreach ($albums as $album): ?>
                    <div class="bg-gray-200 p-4 rounded-lg shadow flex flex-col">
                        <div class="mb-2 h-40 overflow-hidden rounded">
                            <?php if ($album['thumbnail']): ?>
                                <img src="uploads/<?php echo h($album['thumbnail']); ?>" alt="Album thumbnail" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-500">No image</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h2 class="text-xl font-bold mb-2 truncate"><?php echo h($album['name']); ?></h2>
                        <p class="text-sm text-gray-600 mb-2 line-clamp-2"><?php echo h($album['description']); ?></p>
                        <p class="text-xs text-gray-500 mb-4"><?php echo h($album['created_at']); ?></p>
                        <div class="flex flex-col space-y-2 mt-auto">
                            <a href="?id=<?php echo h($album['id']); ?>" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded text-center">表示</a>
                            <?php if ($_SESSION['role'] !== 'view'): ?>
                                <form method="POST" action="album_delete.php" class="inline" onsubmit="return confirm('本当にこのアルバムを削除しますか？この操作は取り消せません。');">
                                    <input type="hidden" name="album_id" value="<?php echo h($album['id']); ?>">
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded">削除</button>
                                </form>
                            <?php endif; ?>
                            <a href="album_add_photos.php?id=<?php echo h($album['id']); ?>" class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded text-center">写真を追加</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo h($current_album['name']); ?></h2>
            <p class="text-gray-600 mb-4"><?php echo h($current_album['description']); ?></p>
            <div class="mb-4 flex justify-between">
                <a href="album_add_photos.php?id=<?php echo h($album_id); ?>" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    写真を追加
                </a>
                <a href="album_view.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                    アルバム一覧
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($photos as $index => $photo): ?>
                    <div class="bg-gray-200 p-4 rounded-lg shadow">
                        <img src="uploads/<?php echo h($photo['file_name']); ?>" alt="Photo" class="w-full h-40 object-cover mb-2 rounded cursor-pointer" onclick="openModal('uploads/<?php echo h($photo['file_name']); ?>', '<?php echo h($photo['comment']); ?>', '<?php echo h($photo['username']); ?>', <?php echo $index; ?>)">
                        <p class="text-sm truncate"><?php echo h(substr($photo['comment'], 0, 50)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">投稿者: <?php echo h($photo['username']); ?></p>
                        <form method="POST" class="mt-2">
                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                            <input type="hidden" name="photo_id" value="<?php echo h($photo['id']); ?>">
                            <input type="hidden" name="album_id" value="<?php echo h($album_id); ?>">
                    <button type="submit" name="remove_from_album" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('本当にこの写真をアルバムから削除しますか？');">
                        アルバムから削除
                    </button>
                <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

   <!-- モーダル -->
<div id="myoModal" class="fixed z-50 inset-0 overflow-hidden hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="special_popup relative bg-white w-full h-full sm:h-auto sm:max-h-[90vh] max-w-3xl mx-auto rounded-lg shadow-xl overflow-hidden flex flex-col">
            <div class="absolute top-0 left-0 right-0 flex justify-between items-center p-4 z-10 bg-opacity-50 bg-gray-800">
                <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="changePhoto(-1)">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="closeModal()">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="changePhoto(1)">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-grow pt-16">
                <img id="modalImage" src="" alt="Full size photo" class="w-full h-auto object-contain">
                <div class="p-4 bg-white">
                    <div class="mt-2">
                        <p id="modalComment" class="text-sm text-gray-500"></p>
                    </div>
                    <?php if ($is_admin_or_editor) : ?>
                        <div id="commentSection" class="mt-4">
                            <form id="commentForm" class="mt-4">
                                <input type="hidden" id="photoId" name="photo_id">
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var modal = document.getElementById("myoModal");
    var modalImg = document.getElementById("modalImage");
    var modalComment = document.getElementById("modalComment");
    var currentPhotoIndex = 0;
    var photos = <?php echo json_encode($photos ?? array()); ?>;

    function openModal(src, comment, username, index) {
        modal.classList.remove('hidden');
        modalImg.src = src;
        modalComment.innerHTML = comment + "<br>投稿者: " + username;
        currentPhotoIndex = index;
    }

    function closeModal() {
        modal.classList.add('hidden');
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
        modalComment.innerHTML = photo.comment + "<br>投稿者: " + photo.username;
    }

    // モーダルの外側をクリックしたときにモーダルを閉じる
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    // キーボードでの操作
    document.onkeydown = function(e) {
        if (!modal.classList.contains('hidden')) {
            if (e.keyCode == 37) { // 左矢印キー
                changePhoto(-1);
            } else if (e.keyCode == 39) { // 右矢印キー
                changePhoto(1);
            } else if (e.keyCode == 27) { // ESCキー
                closeModal();
            }
        }
    }
</script>

    <?php include 'footer_photo.php'; ?>

   
</body>
</html>