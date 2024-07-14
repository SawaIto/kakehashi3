<?php
include("funcs.php");
sschk();
$pdo = db_conn();

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$group_id = $_SESSION['group_id'];
$is_admin_or_editor = ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify');
$is_edit_or_modify = ($_SESSION['role'] == 'edit' || $_SESSION['role'] == 'modify' || $_SESSION['role'] == 'admin');


// 検索とソート
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$query = "SELECT DISTINCT p.*, u.username FROM photos p
          JOIN users u ON p.user_id = u.id
          WHERE p.group_id = :group_id";
$params = [':group_id' => $group_id];

if ($search) {
    $query .= " AND (p.comment LIKE :search OR u.username LIKE :search)";
    $params[':search'] = "%$search%";
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
            padding-top: 64px;
            padding-bottom: 80px;
        }
        @media (max-width: 640px) {
            body {
                padding-top: 128px;
                padding-bottom: 120px;
            }
        }
        .content-wrapper {
            min-height: calc(100vh - 144px);
            overflow-y: auto;
        }
        @media (max-width: 640px) {
            .content-wrapper {
                min-height: calc(100vh - 248px);
            }
        }
        .special_popup {
            width: 90%;
            max-width: 500px;
        }
        img#modalImage {
            max-height: 280px;
        }

        .overflow-y-auto {
        -webkit-overflow-scrolling: touch;
    }

    .modal-content {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}
#modalImage {
    max-height: calc(100vh - 250px);
    width: 100%;
    object-fit: contain;
}

    </style>
</head>

<body class="bg-gray-200" id="body">
    <?php include 'header0.php'; ?>
    <div class="content-wrapper">
        <div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
            <h1 class="text-3xl font-bold mb-6 text-center">写真一覧</h1>

            <div class="flex justify-end mb-4 space-x-2">
            <a href="album_create.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                アルバム作成          
             </a>
            <a href="album_view.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                アルバム一覧          
             </a>
                <a href="photo_upload.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                写真を追加
            </a>
          
        </div>


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
            <?php if ($is_edit_or_modify) : ?>
                <div class="mt-2 flex justify-between">
                    <button onclick="editComment(<?= $index ?>)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">修正</button>
                    <button onclick="deletePhoto(<?= h($photo['id']) ?>)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">削除</button>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
        </div>
    </div>

<!-- View Modal -->
<div id="photoModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" style="margin-bottom: 80px;">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start flex-col">
                    <div class="w-full flex justify-between items-center mb-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            写真詳細
                        </h3>
                        <button type="button" class="text-gray-900 hover:text-blue-700 focus:outline-none text-lg" onclick="closeModal()">
                            閉じる
                        </button>
                    </div>
                    <div class="flex justify-between items-center w-full mb-6">
                        <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2" onclick="prevPhoto()">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2" onclick="nextPhoto()">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-content overflow-y-auto w-full" style="max-height: calc(100vh - 300px);">
                        <img id="modalImage" src="" alt="Full size photo" class="w-full h-auto object-contain mb-4">
                        <p id="modalComment" class="text-sm text-gray-500"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen mb-6 pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" style="margin-bottom: 80px;">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                コメント編集
                            </h3>
                            <button type="button" class="text-gray-900 hover:text-blue-700 focus:outline-none text-lg" onclick="closeEditModal()">
                                閉じる
                            </button>
                        </div>
                        <div class="modal-content overflow-y-auto" style="max-height: calc(100vh - 250px);">
                            <img id="editModalImage" src="" alt="Edit photo" class="w-full h-auto object-contain mb-4">
                            <form id="commentForm">
                                <input type="hidden" id="photoId" name="photo_id">
                                <textarea id="editComment" name="comment" class="w-full p-2 border rounded" rows="3"></textarea>
                                <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">コメントを更新</button>
    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include 'footer_photo.php'; ?>

<script>
    const photos = <?= json_encode($photos) ?>;
    let currentPhotoIndex = 0;

    function openModal(index) {
        currentPhotoIndex = index;
        showPhoto(currentPhotoIndex);
        document.getElementById('photoModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('photoModal').classList.add('hidden');
    }

    function showPhoto(index) {
        const photo = photos[index];
        document.getElementById('modalImage').src = 'uploads/' + photo.file_name;
        document.getElementById('modalComment').textContent = photo.comment;
    }

    function nextPhoto() {
        currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
        showPhoto(currentPhotoIndex);
    }

    function prevPhoto() {
        currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
        showPhoto(currentPhotoIndex);
    }

    function editComment(index) {
        const photo = photos[index];
        document.getElementById('editModalImage').src = 'uploads/' + photo.file_name;
        document.getElementById('photoId').value = photo.id;
        document.getElementById('editComment').value = photo.comment;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
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

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const photoId = document.getElementById('photoId').value;
            const comment = document.getElementById('editComment').value;
            fetch('update_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `photo_id=${photoId}&comment=${encodeURIComponent(comment)}`
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const photoIndex = photos.findIndex(photo => photo.id == photoId);
                        if (photoIndex !== -1) {
                            photos[photoIndex].comment = comment;
                        }
                        closeEditModal();
                        location.reload(); // ページをリロードして更新された写真一覧を表示
                    } else {
                        alert('コメントの更新に失敗しました。');
                    }
                });
        });

        // Hammer.js を使用してスワイプ操作を検出
        const modal = document.getElementById('photoModal');
        const hammer = new Hammer(modal);

        hammer.on('swipeleft', function() {
            nextPhoto();
        });

        hammer.on('swiperight', function() {
            prevPhoto();
        });
    });
</script>
</body>
</html>