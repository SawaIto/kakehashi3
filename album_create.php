<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if (empty($name)) {
        $error = 'アルバム名を入力してください。';
    } else {
        $stmt = $pdo->prepare("INSERT INTO albums (group_id, name, description) VALUES (?, ?, ?)");
        $status = $stmt->execute([$_SESSION['group_id'], $name, $description]);

        if ($status) {
            $_SESSION['success_message'] = 'アルバムが正常に作成されました。';
            redirect('album_view.php');
        } else {
            $error = 'アルバムの作成に失敗しました。';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アルバム作成</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body class="bg-gray-200" id="body">
    <?php include 'header0.php'; ?>
    <div class="container mx-auto mt-20 p-6 bg-white rounded-lg shadow-md max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">アルバム作成</h1>

        <div class="flex justify-end mb-4 space-x-2">
            <a href="album_view.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                アルバム一覧
            </a>
     
                <a href="photo_upload.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                    写真を追加
                </a>
                <a href="photo_view.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                    写真一覧
                </a>
            </div>


            <?php if (isset($error)) : ?>
                <p class="text-red-500 mb-4 text-center"><?= h($error) ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-lg font-semibold">アルバム名：</label>
                    <input type="text" id="name" name="name" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="description" class="block text-lg font-semibold">説明：</label>
                    <textarea id="description" name="description" class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="4"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 text-black font-bold px-4 py-2 rounded-lg text-lg text-center transition duration-300">作成</button>
            </form>

        </div>
        <?php include 'footer_photo.php'; ?>
</body>

</html>