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
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != :current_user_id");
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
    <title><?= $memo ? 'メモ編集' : 'メモ作成' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md max-w-2xl">
        <h1 class="text-3xl font-bold mb-6 text-center"><?= $memo ? 'メモ編集' : 'メモ作成' ?></h1>
        <form action="memo_edit.php" method="post" class="space-y-4">
            <?php if($memo): ?>
                <input type="hidden" name="id" value="<?= $memo['id'] ?>">
            <?php endif; ?>
            <div>
                <label for="category" class="block text-lg font-semibold">カテゴリー:</label>
                <select name="category" id="category" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="買い物" <?= ($memo && $memo['category'] == '買い物') ? 'selected' : '' ?>>買い物</option>
                    <option value="予定" <?= ($memo && $memo['category'] == '予定') ? 'selected' : '' ?>>予定</option>
                    <option value="その他" <?= ($memo && $memo['category'] == 'その他') ? 'selected' : '' ?>>その他</option>
                </select>
            </div>
            <div>
                <label for="title" class="block text-lg font-semibold">表題:</label>
                <input type="text" id="title" name="title" value="<?= $memo ? h($memo['title']) : '' ?>" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="content" class="block text-lg font-semibold">内容:</label>
                <textarea id="content" name="content" rows="4" required class="w-full p-2 border rounded-md border-blue-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><?= $memo ? h($memo['content']) : '' ?></textarea>
            </div>
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_private" class="form-checkbox text-blue-500" <?= $memo && $memo['is_private'] ? 'checked' : '' ?>>
                    <span class="ml-2 text-lg">プライベートメモ（共有しない）</span>
                </label>
            </div>
            <div>
                <p class="text-lg font-semibold mb-2">共有するユーザー:</p>
                <div class="space-y-2">
                    <?php foreach ($users as $user): ?>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="shared_users[]" value="<?= $user['id'] ?>" class="form-checkbox text-blue-500" 
                                <?= (in_array($user['id'], $shared_users)) ? 'checked' : '' ?>>
                            <span class="ml-2"><?= h($user['username']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-lg transition duration-300">
                    <?= $memo ? '更新' : '保存' ?>
                </button>
                <a href="memo_view.php" class="text-blue-500 hover:text-blue-700 transition duration-300">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>