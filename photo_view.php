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
$is_admin_or_editor = ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify');

// 検索とソート
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';

$query = "SELECT DISTINCT p.*, u.username FROM photos p
          JOIN users u ON p.user_id = u.id
          LEFT JOIN photo_tags pt ON p.id = pt.photo_id
          WHERE p.group_id = :group_id";
$params = [':group_id' => $group_id];

if ($search) {
    $query .= " AND (p.comment LIKE :search OR pt.tag LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($tag) {
    $query .= " AND pt.tag = :tag";
    $params[':tag'] = $tag;
}

if ($sort === 'oldest') {
    $query .= " ORDER BY p.upload_date ASC";
} else {
    $query .= " ORDER BY p.upload_date DESC";
}

$stmt = $pdo->prepare($query);
$status = $stmt->execute($params);

if ($status == false) {
    sql_error($stmt);
} else {
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// タグ一覧の取得
$stmt = $pdo->prepare("SELECT DISTINCT tag FROM photo_tags WHERE photo_id IN (SELECT id FROM photos WHERE group_id = ?)");
$stmt->execute([$group_id]);
$tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>写真一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://hammerjs.github.io/dist/hammer.min.js"></script>
    <style>
        body {
            font-family: "メイリオ", Meiryo, sans-serif;
        }
    </style>
</head>
<?php include 'header0.php'; ?>
<body class="bg-gray-200" id="body">
<?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6 text-center">写真一覧</h1>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form action="" method="GET" class="mb-4">
            <div class="flex flex-wrap -mx-2 mb-4">
                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                    <input type="text" name="search" placeholder="検索..." value="<?= h($search) ?>" class="w-full p-2 border rounded">
                </div>
                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                    <select name="sort" class="w-full p-2 border rounded">
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>新しい順</option>
                        <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>古い順</option>
                    </select>
                </div>
                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                    <select name="tag" class="w-full p-2 border rounded">
                        <option value="">タグ選択</option>
                        <?php foreach ($tags as $t) : ?>
                            <option value="<?= h($t) ?>" <?= $tag == $t ? 'selected' : '' ?>><?= h($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-center space-x-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                    検索・ソート
                </button>
                <a href="photo_view.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                    検索・ソート解除
                </a>
            </div>
        </form>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($photos as $index => $photo) : ?>
                <div class="bg-gray-200 p-4 rounded-lg shadow">
                    <img src="uploads/<?= h($photo['file_name']) ?>" alt="Photo" class="w-full h-40 object-cover mb-2 rounded cursor-pointer" onclick="openModal(<?= $index ?>)">
                    <p class="text-sm truncate"><?= h(substr($photo['comment'], 0, 50)) ?></p>
                    <p class="text-xs text-gray-500 mt-1">投稿者: <?= h($photo['username']) ?></p>
                    <p class="text-xs text-gray-500"><?= h($photo['upload_date']) ?></p>
                    <?php if ($is_admin_or_editor) : ?>
                        <div class="mt-2 flex justify-between">
                            <a href="photo_edit.php?id=<?= h($photo['id']) ?>" class="text-blue-500 hover:text-blue-700">編集</a>
                            <button onclick="deletePhoto(<?= h($photo['id']) ?>)" class="text-red-500 hover:text-red-700">削除</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-6 text-center space-y-2 sm:space-y-0 sm:space-x-2 flex flex-col sm:flex-row justify-center items-center">
            <a href="photo_upload.php" class="w-full sm:w-auto bg-blue-400 hover:bg-blue-500 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300 mb-2 sm:mb-0">
                新規写真アップロード
            </a>
            <a href="album_create.php" class="w-full sm:w-auto bg-green-400 hover:bg-green-500 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300 mb-2 sm:mb-0">
                アルバムを作成する
            </a>
            <a href="album_view.php" class="w-full sm:w-auto bg-green-400 hover:bg-green-500 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300 mb-2 sm:mb-0">
                アルバム一覧
            </a>
            <a href="home.php" class="w-full sm:w-auto bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-lg transition duration-300">
                ホームに戻る
            </a>
        </div>
    </div>

<style>
.special_popup{
    width:90%;
    max-width: 500px;
}
img#modalImage {
    max-height: 280px;
}
</style>

    <!-- Modal -->
    <div id="photoModal" class="fixed z-50 inset-0 overflow-hidden hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-0">
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="special_popup relative bg-white w-full h-full sm:h-auto sm:max-h-[90vh] max-w-3xl mx-auto rounded-lg shadow-xl overflow-hidden flex flex-col">
                <div class="absolute top-0 left-0 right-0 flex justify-between items-center p-4 z-10 bg-opacity-50 bg-gray-800">
                    <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="prevPhoto()">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="closeModal()">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="nextPhoto()">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-grow pt-16">
                    <img id="modalImage" src="" alt="Full size photo" class="w-full h-auto object-contain">
                    <div class="p-4 bg-white">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            コメント
                        </h3>
                        <div class="mt-2">
                            <p id="modalComment" class="text-sm text-gray-500"></p>
                        </div>
                        <?php if ($is_admin_or_editor) : ?>
                            <div id="commentSection" class="mt-4">
                                <h4 class="text-md font-medium text-gray-900">コメント一覧</h4>
                                <div id="commentList" class="mt-2"></div>
                                <form id="commentForm" class="mt-4">
                                    <input type="hidden" id="photoId" name="photo_id">
                                    <textarea id="newComment" name="comment" class="w-full p-2 border rounded" placeholder="コメントを入力..."></textarea>
                                    <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">コメントを追加</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer id="footer">
    <style>
        html {
            scroll-behavior: smooth;
        }
        .fixed-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .scroll-button {
            background-color: #1f2937;
            color: white;
            padding: 10px;
            border-radius: 50%;
            text-align: center;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .scroll-button:hover {
            background-color: #111827; /* bg-gray-900 */
        }
    </style>
        フッター（仮）
        <div class="fixed-buttons">
            <a href="#body" class="scroll-button">↑</a>
            <a href="#footer" class="scroll-button">↓</a>
        </div>
    </footer>

    <script>
        const photos = <?= json_encode($photos) ?>;
        let currentPhotoIndex = 0;

        function openModal(index) {
            currentPhotoIndex = index;
            showPhoto(currentPhotoIndex);
            document.getElementById('photoModal').classList.remove('hidden');
            window.history.pushState({
                photoIndex: currentPhotoIndex
            }, "", `#photo-${currentPhotoIndex}`);
        }

        function closeModal() {
            document.getElementById('photoModal').classList.add('hidden');
            window.history.pushState(null, "", window.location.pathname);
        }

        function showPhoto(index) {
            const photo = photos[index];
            document.getElementById('modalImage').src = 'uploads/' + photo.file_name;
            document.getElementById('modalComment').textContent = photo.comment;
            if (document.getElementById('photoId')) {
                document.getElementById('photoId').value = photo.id;
            }
            if (<?= json_encode($is_admin_or_editor) ?>) {
                loadComments(photo.id);
            }
        }

        function nextPhoto() {
            currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
            showPhoto(currentPhotoIndex);
            window.history.pushState({
                photoIndex: currentPhotoIndex
            }, "", `#photo-${currentPhotoIndex}`);
        }

        function prevPhoto() {
            currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
            showPhoto(currentPhotoIndex);
            window.history.pushState({
                photoIndex: currentPhotoIndex
            }, "", `#photo-${currentPhotoIndex}`);
        }

        // Hammer.js を使用してスワイプ操作を検出
        const modal = document.getElementById('photoModal');
        const hammer = new Hammer(modal);

        hammer.on('swipeleft', function() {
            nextPhoto();
        });

        hammer.on('swiperight', function() {
            prevPhoto();
        });

        // ブラウザの戻るボタンの処理
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.photoIndex !== undefined) {
                currentPhotoIndex = event.state.photoIndex;
                showPhoto(currentPhotoIndex);
            } else {
                closeModal();
            }
        });

        function loadComments(photoId) {
            fetch(`get_comments.php?photo_id=${photoId}`)
                .then(response => response.json())
                .then(comments => {
                    const commentList = document.getElementById('commentList');
                    commentList.innerHTML = '';
                    comments.forEach(comment => {
                        const commentElement = document.createElement('div');
                        commentElement.className = 'mb-2 p-2 bg-gray-200 rounded';
                        commentElement.textContent = `${comment.username}: ${comment.comment}`;
                        commentList.appendChild(commentElement);
                    });
                });
        }

        if (document.getElementById('commentForm')) {
            document.getElementById('commentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const photoId = document.getElementById('photoId').value;
                const comment = document.getElementById('newComment').value;
                fetch('add_comment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `photo_id=${photoId}&comment=${encodeURIComponent(comment)}`
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            document.getElementById('newComment').value = '';
                            loadComments(photoId);
                        } else {
                            alert('コメントの追加に失敗しました。');
                        }
                    });
            });
        }

        function deletePhoto(photoId) {
            if (confirm('本当にこの写真を削除しますか？')) {
                fetch('photo_delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `photo_id=${photoId}`
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('写真が削除されました。');
                            location.reload(); // ページをリロードして更新された写真一覧を表示
                        } else {
                            alert('写真の削除に失敗しました。');
                        }
                    });
            }
        }
    </script>
</body>

</html>
