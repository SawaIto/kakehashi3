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
            font-family: sans-serif;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        @media (max-width: 640px) {
            body {
                padding-top: 90px;
                padding-bottom: 40px;
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

.floating-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: rgba(59, 130, 246, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    writing-mode: vertical-rl;
    text-orientation: upright;
    z-index: 1000;
}

.floating-button:hover {
    background-color: rgba(29, 78, 216, 0.8);
}
    </style>
</head>

<body class="bg-blue-50" id="body">
    <?php include 'header0.php'; ?>
    <div class="content-wrapper">
        <div class="container mx-auto p-6 bg-white rounded shadow-md">
            <h1 class="text-3xl font-bold mb-6 text-center">写真一覧</h1>

            <div class="flex justify-end mb-4 space-x-2">
            <a href="album_create.php" class="bg-purple-500 hover:bg-purple-600 text-black font-bold py-2 px-2 sm:px-4 rounded text-xs sm:text-base transition duration-300">
                アルバム作成          
             </a>
            <a href="album_view.php" class="bg-purple-500 hover:bg-purple-600 text-black font-bold py-2 px-2 sm:px-4 rounded text-xs sm:text-base transition duration-300">
                アルバム一覧          
             </a>
                <a href="photo_upload.php" class="bg-green-500 hover:bg-green-600 text-black font-bold py-2 px-2 sm:px-4 rounded text-xs sm:text-base transition duration-300">
                写真を追加
            </a>        
        </div>
            <?php if (isset($_SESSION['success_message'])) : ?>
                <p class="text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <form action="" method="GET" class="mb-4">
                <div class="flex flex-wrap -mx-2 mb-4">
                    <!-- <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                        <input type="text" name="search" placeholder="検索..." value="<?= h($search) ?>" class="w-full p-2 border rounded">
                    </div> -->
                    <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                        <select name="sort" class="w-full p-2 border rounded">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>新しい順</option>
                            <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>古い順</option>
                        </select>
                    </div>
                </div>
                <!-- <div class="flex justify-center space-x-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                        検索・ソート
                    </button>
                    <a href="photo_view.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                        検索・ソート解除
                    </a>
                </div> -->
            </form>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php foreach ($photos as $index => $photo) : ?>
        <div class="bg-gray-200 p-4 rounded shadow">
            <img src="uploads/<?= h($photo['file_name']) ?>" alt="Photo" class="w-full h-40 object-cover mb-2 rounded cursor-pointer" onclick="openModal(<?= $index ?>)">
            <p class="text-sm truncate"><?= h(substr($photo['comment'], 0, 50)) ?></p>
            <p class="text-xs text-gray-500 mt-1">投稿者: <?= h($photo['username']) ?></p>
            <p class="text-xs text-gray-500"><?= h($photo['upload_date']) ?></p>
            <?php if ($is_edit_or_modify) : ?>
                <div class="mt-2 flex justify-between">
                    <button onclick="editComment(<?= $index ?>)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">編集</button>
                    <button onclick="deletePhoto(<?= h($photo['id']) ?>)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">削除</button>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
        </div>
    </div>

<!-- View Modal -->
<div id="photoModal" class="fixed z-50 inset-0 overflow-hidden hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-0">
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="special_popup relative bg-white w-full h-full sm:h-auto sm:max-h-[90vh] max-w-3xl mx-auto rounded shadow-xl overflow-hidden flex flex-col">
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

<!-- Edit Modal -->
<div id="editModal" class="fixed z-80 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
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
                        <div class="modal-content overflow-y-auto" style="max-height: calc(100vh - 300px);">
                            <img id="editModalImage" src="" alt="Edit photo" class="w-full h-auto object-contain mb-4">
                            <form id="commentForm">
                                <input type="hidden" id="photoId" name="photo_id">
                                <textarea id="editComment" name="comment" class="w-full p-2 border rounded mb-4" rows="3"></textarea>
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
    <!-- <?php include 'footer_photo.php'; ?> -->

    <button id="scrollToTopBtn" class="floating-button">一番上に移動</button>

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
                    location.reload();
                } else {
                    alert('写真の削除に失敗しました。');
                }
            });
        }
    }

    function scrollFunction() {
        const scrollToTopBtn = document.getElementById("scrollToTopBtn");
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            scrollToTopBtn.style.display = "flex";
        } else {
            scrollToTopBtn.style.display = "none";
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
                    location.reload();
                } else {
                    alert('コメントの更新に失敗しました。');
                }
            });
        });

        const modal = document.getElementById('photoModal');
        const hammer = new Hammer(modal);

        hammer.on('swipeleft', function() {
            nextPhoto();
        });

        hammer.on('swiperight', function() {
            prevPhoto();
        });

        const scrollToTopBtn = document.getElementById("scrollToTopBtn");
        scrollToTopBtn.style.display = "none";
        
        scrollToTopBtn.onclick = function() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };
    });

    window.onscroll = scrollFunction;
</script>
</body>
    <p class="mt-10 text-sm text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>
</html>