<?php
require_once 'funcs.php';
sschk();
$pdo = db_conn();

// チャットルームID
$chat_id = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 1;
// 現在のユーザーID
$current_user_id = $_SESSION["user_id"];  // 仮の値、セッションから取得するなど

// アップロード許可されるファイルタイプ
$allowed_extensions = ['jpg', 'jpeg', 'png'];

// echo "<pre>";
// var_dump($_POST);
// echo "</pre>";

// echo "<pre>";
// var_dump($_FILES);
// echo "</pre>";

// echo $_SERVER["DOCUMENT_ROOT"];

// メッセージ投稿
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'] ?? '';
    $message_type = 'text';  // 今回はテキストのみ扱います
    $file_url = null;

    // ファイルがアップロードされているか確認
    if (isset($_FILES['file'])) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        // ファイルの拡張子が許可されているかチェック
        if (in_array($file_ext, $allowed_extensions)) {
            // 一意なファイル名を生成して保存
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_dir = '/uploads/';
            $file_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $file_path)) {
                $file_url = $file_path;
                $message_type = 'image';  // 画像メッセージとして設定
            } else {
                echo "ファイルのアップロードに失敗しました。";
                echo "エラー: " . $_FILES['file']['error'];
            }
        } else {
            echo "許可されていないファイル形式です。";
        }
    }

    // メッセージまたはファイルがある場合にデータベースに保存
    if (!empty($message) || !empty($file_url)) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (chat_id, user_id, message, message_type, file_url, created_at)
                               VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$chat_id, $current_user_id, $message, $message_type, $file_url]);
    }
}

// メッセージの取得
$stmt = $pdo->prepare("SELECT cm.*, u.username FROM chat_messages cm
                       JOIN users u ON cm.user_id = u.id
                       WHERE cm.chat_id = ? ORDER BY cm.created_at ASC");
$stmt->execute([$chat_id]);
$messages = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>予定登録</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="styles/main.css">
<style>
body {display: flex;flex-direction: column;min-height: 100vh;}
main {flex: 1;display: flex;flex-direction: column;padding-top: 64px;}
.content-wrapper {flex: 1;overflow-y: auto;}
.chat-box {width: 100%;height: 400px;border: 1px solid #ccc;overflow-y: scroll;padding: 10px;}
.message {
    padding: 10px;
    margin: 14px 0;
    border: 1px solid gray;
    border-radius: 10px;
    width: 80%;
}
.message .username {font-weight: bold;}
.message .timestamp {text-align:right;font-size: 0.8em;color: gray;}
.chat-form {margin-top: 10px;}
.my_post{background:#95f68b;margin-left: 20%;}
.others_post{background:white;}

.chat-form{padding:4px}
.chat-form form{
    display: flex;
    gap: 10px;
    border-top: solid 2px gray;
    padding-top: 8px;
}
.chat-form input{border: solid 1px gray;width: 84%;border-radius: 4px;padding: 3px;}
.chat-form button{
    width: 12%;
    background: blue;
    color: white;
    border-radius: 4px;
    padding: 4px;
    font-weight: bold;
}
</style>
</head>
<body class="bg-blue-50" id=body>
<?php include 'header0.php'; ?>

<main>
    <div class="content-wrapper">
        <div class="container mx-auto p-6">
        <div class="container mx-auto mt-20 p-2 bg-white rounded shadow-md max-w-4xl">
            <h1 class="text-3xl font-bold mb-6 text-center">トーク</h1>

                <div class="chat-box">
                    <?php foreach ($messages as $message): ?>
                        <?
                        if($message["user_id"] == $_SESSION["user_id"]){
                            $class = "my_post";
                        }else{
                            $class = "others_post";
                        }
                        ?>

                        <div class="message <?=$class?>">
                            <div class="username"><?= htmlspecialchars($message['username']) ?></div>
                            <?php if ($message['message_type'] == 'image' && !empty($message['file_url'])): ?>
                                <img src="uploads/<?= h($message['file_url']) ?>" style="max-width: 200px;">
                            <?php endif; ?>
                            <span class="text"><?= htmlspecialchars($message['message']) ?></span>
                            <div class="timestamp"><?= $message['created_at'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="chat-form">
                    <form action="chat_room.php?chat_id=<?= $chat_id ?>" method="POST" enctype="multipart/form-data">
                        <input type="text" name="message" placeholder="メッセージを入力...">
                        <input type="file" name="file">
                        <button type="submit">送信</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
