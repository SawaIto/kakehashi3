<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

// アルバム一覧を取得
$stmt = $pdo->prepare("SELECT id, name FROM albums WHERE group_id = ?");
$stmt->execute([$_SESSION['group_id']]);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_dir = 'uploads/';
    $max_file_size = 5 * 1024 * 1024; // 5MB に制限
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_files = 3;

    $uploaded_files = $_FILES['photos'];
    $file_count = count($uploaded_files['name']);

    if ($file_count > $max_files) {
        echo json_encode(['error' => "一度にアップロードできる写真は{$max_files}枚までです。"]);
        exit;
    }

    $success_count = 0;
    $uploaded_photo_ids = [];

    for ($i = 0; $i < $file_count; $i++) {
        $file_name = $uploaded_files['name'][$i];
        $file_tmp = $uploaded_files['tmp_name'][$i];
        $file_type = $uploaded_files['type'][$i];
        $file_size = $uploaded_files['size'][$i];

        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['error' => "許可されていないファイル形式です: {$file_name}"]);
            continue;
        }

        if ($file_size > $max_file_size) {
            echo json_encode(['error' => "ファイルサイズが大きすぎます: {$file_name}"]);
            continue;
        }

        $image = imagecreatefromstring(file_get_contents($file_tmp));
        if (!$image) {
            echo json_encode(['error' => "画像の処理に失敗しました: {$file_name}"]);
            continue;
        }

        $max_width = 1024;
        $max_height = 768;
        list($width, $height) = getimagesize($file_tmp);
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = $width * $ratio;
        $new_height = $height * $ratio;

        $resized_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        $new_file_name = uniqid('photo_') . '.jpg';
        $upload_path = $upload_dir . $new_file_name;

        imagejpeg($resized_image, $upload_path, 60);

        imagedestroy($image);
        imagedestroy($resized_image);

        $comment = $_POST['comments'][$i] ?? '';
        $stmt = $pdo->prepare("INSERT INTO photos (file_name, comment, upload_date, user_id, group_id) VALUES (?, ?, NOW(), ?, ?)");
        if ($stmt->execute([$new_file_name, $comment, $_SESSION['user_id'], $_SESSION['group_id']])) {
            $success_count++;
            $uploaded_photo_ids[] = $pdo->lastInsertId();
        }
    }

    if (!empty($uploaded_photo_ids)) {
        $album_id = $_POST['album_id'];
        if ($album_id == 'new' && !empty($_POST['new_album_name'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM albums WHERE name = ? AND group_id = ?");
            $stmt->execute([$_POST['new_album_name'], $_SESSION['group_id']]);
            $album_exists = $stmt->fetchColumn();

            if ($album_exists) {
                echo json_encode(['error' => "同じ名前のアルバムが既に存在します。"]);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO albums (name, group_id) VALUES (?, ?)");
            $stmt->execute([$_POST['new_album_name'], $_SESSION['group_id']]);
            $album_id = $pdo->lastInsertId();
        }

        if ($album_id != 'none') {
            foreach ($uploaded_photo_ids as $photo_id) {
                $stmt = $pdo->prepare("INSERT INTO album_photos (album_id, photo_id) VALUES (?, ?)");
                $stmt->execute([$album_id, $photo_id]);
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => "{$success_count}枚の写真がアップロードされました。"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>写真アップロード</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js"></script>
    <style>
        body { padding-bottom: 100px; }
        .content-wrapper { min-height: calc(100vh - 180px); overflow-y: auto; }
    </style>
</head>
<body class="bg-blue-50">
    <?php include 'header0.php'; ?>
    <div class="content-wrapper">
        <div class="container mx-auto mt-20 p-4 bg-white rounded shadow-md max-w-2xl">
            <h1 class="text-3xl font-bold mb-6 text-center">写真アップロード</h1>
            <div id="error-message" class="text-red-500 mb-4 text-center hidden"></div>
            <form id="upload-form" class="space-y-4">
                <div class="mb-4">
                    <label for="photos" class="block text-sm font-medium text-gray-700">写真を選択（最大3枚）:</label>
                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple required class="mt-1 block w-full">
                </div>
                <div id="imagePreview" class="grid grid-cols-3 gap-4 mb-4"></div>
                <div id="commentFields"></div>
                <div class="mb-4">
                    <label for="album_id" class="block text-sm font-medium text-gray-700">アルバムを選択:</label>
                    <select id="album_id" name="album_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="none">アルバムに追加しない</option>
                        <?php foreach ($albums as $album): ?>
                            <option value="<?= h($album['id']) ?>"><?= h($album['name']) ?></option>
                        <?php endforeach; ?>
                        <option value="new">新しいアルバムを作成</option>
                    </select>
                </div>
                <div id="newAlbumField" class="mb-4 hidden">
                    <label for="new_album_name" class="block text-sm font-medium text-gray-700">新しいアルバム名:</label>
                    <input type="text" id="new_album_name" name="new_album_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div id="progress-bar" class="w-full bg-gray-200 rounded-full h-2.5 mb-4 hidden">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <button type="submit" class="w-full bg-amber-700 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded">アップロード</button>
                <div class="container mx-auto px-4">
        <div class="flex justify-end mb-4 space-x-2">
        <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-black font-bold py-2 px-2 rounded text-xs sm:text-base transition duration-300">
                ホーム
            </a>
        <a href="album_view.php" class="bg-purple-500 hover:bg-purple-600 text-black font-bold py-2 px-2 sm:px-4 rounded text-xs sm:text-base transition duration-300">
                アルバム一覧          
             </a>
            <a href="photo_view.php" class="bg-pink-400 hover:bg-pink-500 text-black font-bold py-2 px-2 rounded text-xs sm:text-base transition duration-300">
                写真一覧
            </a>
        </div>

        <div id="searchFilter" class="hidden mb-4 bg-gray-200 p-4 rounded">
            <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-end space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex-grow">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">開始日:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= h($start_date) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="flex-grow">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">終了日:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= h($end_date) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-black font-bold py-2 px-4 rounded text-sm sm:text-base transition duration-300">検索</button>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-sm sm:text-base transition duration-300">リセット</a>
                </div>
            </form>
        </div>
         
            </form>
        </div>
    </div>
    <!-- <?php include 'footer_photo.php'; ?> -->

    <script>
        const form = document.getElementById('upload-form');
        const photosInput = document.getElementById('photos');
        const imagePreview = document.getElementById('imagePreview');
        const commentFields = document.getElementById('commentFields');
        const progressBar = document.getElementById('progress-bar');
        const errorMessage = document.getElementById('error-message');

        photosInput.addEventListener('change', previewImages);
        form.addEventListener('submit', uploadPhotos);

        document.getElementById('album_id').addEventListener('change', function() {
            const newAlbumField = document.getElementById('newAlbumField');
            newAlbumField.classList.toggle('hidden', this.value !== 'new');
        });

        function previewImages(event) {
            imagePreview.innerHTML = '';
            commentFields.innerHTML = '';
            const files = event.target.files;

            for (let i = 0; i < Math.min(files.length, 3); i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-32 object-cover rounded';
                    div.appendChild(img);
                    imagePreview.appendChild(div);

                    const commentField = document.createElement('div');
                    commentField.className = 'mb-4';
                    commentField.innerHTML = `
                        <label for="comment${i}" class="block text-sm font-medium text-gray-700">コメント (写真 ${i + 1}):</label>
                        <textarea id="comment${i}" name="comments[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                    `;
                    commentFields.appendChild(commentField);
                }

                reader.readAsDataURL(file);
            }
        }

        function uploadPhotos(event) {
            event.preventDefault();
            const formData = new FormData(form);
            const files = photosInput.files;

            if (files.length === 0) {
                showError('写真を選択してください。');
                return;
            }

            if (files.length > 3) {
                showError('一度にアップロードできる写真は3枚までです。');
                return;
            }

            progressBar.classList.remove('hidden');
            errorMessage.classList.add('hidden');

            const compressedFiles = [];
            let processedFiles = 0;

            for (let i = 0; i < files.length; i++) {
                new Compressor(files[i], {
                    quality: 0.6,
                    maxWidth: 1024,
                    maxHeight: 768,
                    success(result) {
                        compressedFiles.push(result);
                        processedFiles++;

                        if (processedFiles === files.length) {
                            sendCompressedFiles(formData, compressedFiles);
                        }
                    },
                    error(err) {
                        showError('画像の圧縮中にエラーが発生しました: ' + err.message);
                    },
                });
            }
        }

        function sendCompressedFiles(formData, compressedFiles) {
            formData.delete('photos[]');
            compressedFiles.forEach(file => formData.append('photos[]', file, file.name));

            fetch('photo_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showError(data.error);
                } else {
                    alert(data.message);
                    window.location.href = 'photo_view.php';
                }
            })
            .catch(error => {
                showError('アップロード中にエラーが発生しました: ' + error.message);
            })
            .finally(() => {
                progressBar.classList.add('hidden');
            });
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
            progressBar.classList.add('hidden');
        }
    </script>
</body>
<p class="mt-10 text-sm text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>

</html>