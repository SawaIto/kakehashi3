<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

$is_view_role = ($_SESSION['role'] == 'view');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = $is_view_role ? '' : $_POST['comment'];
    $tags = $is_view_role ? [] : (isset($_POST['tags']) ? explode(',', $_POST['tags']) : []);
    $album_id = $is_view_role ? null : $_POST['album_id'];
    $new_album_name = $is_view_role ? '' : $_POST['new_album_name'];
    $file = $_FILES['photo'];

    if (empty($file['name'])) {
        $error = '写真を選択してください。';
    } else {
        $filename = uniqid() . '.jpg'; // JPEG形式に変換するためファイル名を設定
        $upload_dir = __DIR__ . '/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $upload_file = $upload_dir . $filename;

        // 画像をJPEG形式に変換して保存
        $image_info = getimagesize($file['tmp_name']);
        $image_type = $image_info[2];

        if ($image_type == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($file['tmp_name']);
        } elseif ($image_type == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($file['tmp_name']);
        } elseif ($image_type == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($file['tmp_name']);
        } else {
            $error = '対応していない画像形式です。';
            $image = false;
        }

        if ($image) {
            // 画質を落としてJPEG形式で保存
            $quality = 75; // 画質を0から100の範囲で設定
            if (imagejpeg($image, $upload_file, $quality)) {
                imagedestroy($image);

                // 新しいアルバムを作成（view権限以外）
                if (!$is_view_role && !empty($new_album_name)) {
                    $stmt = $pdo->prepare("INSERT INTO albums (group_id, name) VALUES (?, ?)");
                    $stmt->execute([$_SESSION['group_id'], $new_album_name]);
                    $album_id = $pdo->lastInsertId();
                }

                $stmt = $pdo->prepare("INSERT INTO photos (group_id, user_id, file_name, comment) VALUES (?, ?, ?, ?)");
                $status = $stmt->execute([$_SESSION['group_id'], $_SESSION['user_id'], $filename, $comment]);

                if ($status) {
                    $photo_id = $pdo->lastInsertId();

                    // タグの保存（view権限以外）
                    if (!$is_view_role) {
                        foreach ($tags as $tag) {
                            $tag = trim($tag);
                            if (!empty($tag)) {
                                $stmt = $pdo->prepare("INSERT INTO photo_tags (photo_id, tag) VALUES (?, ?)");
                                $stmt->execute([$photo_id, $tag]);
                            }
                        }

                        // アルバムへの追加
                        if (!empty($album_id)) {
                            $stmt = $pdo->prepare("INSERT INTO album_photos (album_id, photo_id) VALUES (?, ?)");
                            $stmt->execute([$album_id, $photo_id]);
                        }
                    }

                    $_SESSION['success_message'] = '写真が正常にアップロードされました。';
                    redirect('photo_view.php');
                } else {
                    $error = '写真のアップロードに失敗しました。';
                }
            } else {
                $error = '画像の変換に失敗しました。';
            }
        }
    }
}

// アルバム一覧の取得（view権限以外）
if (!$is_view_role) {
    $stmt = $pdo->prepare("SELECT id, name FROM albums WHERE group_id = ?");
    $stmt->execute([$_SESSION['group_id']]);
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>写真アップロード</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "メイリオ", Meiryo, sans-serif; }
    </style>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">写真アップロード</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="photo" class="block text-lg font-semibold">写真：</label>
                <input type="file" id="photo" name="photo" accept="image/*" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <?php if (!$is_view_role): ?>
                <div>
                    <label for="comment" class="block text-lg font-semibold">コメント：</label>
                    <textarea id="comment" name="comment" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4"></textarea>
                </div>
                <div>
                    <label for="tags" class="block text-lg font-semibold">タグ：</label>
                    <input type="text" id="tags" name="tags" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="カンマ区切りで入力">
                </div>
                <div>
                    <label for="album_id" class="block text-lg font-semibold">既存のアルバム：</label>
                    <select id="album_id" name="album_id" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">選択してください</option>
                        <?php foreach ($albums as $album): ?>
                            <option value="<?= h($album['id']) ?>"><?= h($album['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="new_album_name" class="block text-lg font-semibold">新規アルバム名：</label>
                    <input type="text" id="new_album_name" name="new_album_name" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="新しいアルバム名を入力">
                </div>
            <?php endif; ?>
            <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">アップロード</button>
        </form>

        <div class="mt-6 text-center">
            <a href="photo_view.php" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded text-xl transition duration-300">
                写真一覧に戻る
            </a>
        </div>
    </div>
</body>
</html>
