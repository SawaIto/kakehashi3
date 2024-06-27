<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザーの権限チェック
if ($_SESSION['kanri_flg'] != 1 && $_SESSION['modify_flg'] != 1) {
    redirect('memo_view.php');
}

$user_id = $_SESSION['user_id'];

// ユーザー一覧の取得（現在のユーザーを除く）
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE id != :current_user_id");
$stmt->bindValue(':current_user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 既存のメモを編集する場合
$memo = null;
$shared_users = [];
if(isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM memos WHERE id = :id AND user_id = :user_id");
    $stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $status = $stmt->execute();
    if($status) {
        $memo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 共有ユーザーの取得
        $stmt = $pdo->prepare("SELECT user_id FROM memo_shares WHERE memo_id = :memo_id");
        $stmt->bindValue(':memo_id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $shared_users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// メモの保存処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;
    $shared_users = isset($_POST['shared_users']) ? $_POST['shared_users'] : [];
    
    $pdo->beginTransaction();
    
    try {
        if(isset($_POST['id'])) {
            // 既存のメモを更新
            $stmt = $pdo->prepare("UPDATE memos SET category = :category, title = :title, content = :content, is_private = :is_private WHERE id = :id AND user_id = :user_id");
            $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        } else {
            // 新しいメモを挿入
            $stmt = $pdo->prepare("INSERT INTO memos (category, title, content, user_id, is_private) VALUES (:category, :title, :content, :user_id, :is_private)");
        }
        
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':is_private', $is_private, PDO::PARAM_BOOL);
        $stmt->execute();
        
        $memo_id = isset($_POST['id']) ? $_POST['id'] : $pdo->lastInsertId();
        
        // 共有情報の更新
        $stmt = $pdo->prepare("DELETE FROM memo_shares WHERE memo_id = :memo_id");
        $stmt->bindValue(':memo_id', $memo_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if (!$is_private) {
            foreach ($shared_users as $shared_user_id) {
                $stmt = $pdo->prepare("INSERT INTO memo_shares (memo_id, user_id) VALUES (:memo_id, :user_id)");
                $stmt->bindValue(':memo_id', $memo_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $shared_user_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        
        $pdo->commit();
        redirect('memo_view.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title><?= $memo ? 'メモ編集' : 'メモ作成' ?></title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-6 text-center"><?= $memo ? 'メモ編集' : 'メモ作成' ?></h2>
            <form action="memo_edit.php" method="post">
                <?php if($memo): ?>
                    <input type="hidden" name="id" value="<?= $memo['id'] ?>">
                <?php endif; ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="category">
                        カテゴリー:
                    </label>
                    <select name="category" id="category" class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="買い物" <?= ($memo && $memo['category'] == '買い物') ? 'selected' : '' ?>>買い物</option>
                        <option value="予定" <?= ($memo && $memo['category'] == '予定') ? 'selected' : '' ?>>予定</option>
                        <option value="その他" <?= ($memo && $memo['category'] == 'その他') ? 'selected' : '' ?>>その他</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="title">
                        表題:
                    </label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="title" type="text" name="title" value="<?= $memo ? h($memo['title']) : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="content">
                        内容:
                    </label>
                    <textarea class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="content" name="content" rows="4" required><?= $memo ? h($memo['content']) : '' ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_private" class="form-checkbox" <?= $memo && $memo['is_private'] ? 'checked' : '' ?>>
                        <span class="ml-2">プライベートメモ（共有しない）</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2">共有するユーザー:</label>
                    <?php foreach ($users as $user): ?>
                        <label class="inline-flex items-center mt-3">
                            <input type="checkbox" name="shared_users[]" value="<?= $user['id'] ?>" class="form-checkbox" 
                                <?= (in_array($user['id'], $shared_users)) ? 'checked' : '' ?>>
                            <span class="ml-2"><?= h($user['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center justify-between">
                    <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="<?= $memo ? '更新' : '保存' ?>">
                    <a href="memo_view.php" class="text-blue-500 hover:text-blue-700">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html> 