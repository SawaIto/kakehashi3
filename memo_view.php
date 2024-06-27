<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザーIDの確認
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin_or_editor = ($_SESSION['kanri_flg'] == 1 || $_SESSION['modify_flg'] == 1);

// メモの取得（自分のメモと共有されたメモ）
$stmt = $pdo->prepare("
    SELECT m.*, u.name as owner_name, 
    GROUP_CONCAT(su.name SEPARATOR ', ') as shared_with
    FROM memos m
    LEFT JOIN users u ON m.user_id = u.id
    LEFT JOIN memo_shares ms ON m.id = ms.memo_id
    LEFT JOIN users su ON ms.user_id = su.id
    WHERE m.user_id = :user_id OR ms.user_id = :user_id
    GROUP BY m.id
    ORDER BY m.id DESC
");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
if($status == false) {
    sql_error($stmt);
} else {
    $memos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// メモの削除処理
if($is_admin_or_editor && isset($_POST['delete'])) {
    $delete_id = $_POST['delete'];
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("DELETE FROM memos WHERE id = :id");
        $stmt->bindValue(':id', $delete_id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE FROM memo_shares WHERE memo_id = :memo_id");
        $stmt->bindValue(':memo_id', $delete_id, PDO::PARAM_INT);
        $stmt->execute();

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
    <title>メモ閲覧</title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto px-4 py-8 mt-32 sm:mt-20 flex-grow">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">メモ一覧</h2>
                <?php if($is_admin_or_editor): ?>
                    <a href="memo_edit.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        新規メモ追加
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if(empty($memos)): ?>
                <p class="text-center text-gray-600">表示できるメモはありません。</p>
            <?php else: ?>
                <?php foreach ($memos as $memo): ?>
                    <div class="bg-gray-100 p-4 mb-4 rounded">
                        <h4 class="font-bold text-xl"><?= h($memo['title']) ?></h4>
                        <p class="text-gray-600 mb-2">カテゴリー: <?= h($memo['category']) ?></p>
                        <p class="whitespace-pre-wrap"><?= h($memo['content']) ?></p>
                        <p class="text-sm text-gray-500 mt-2">
                            作成者: <?= h($memo['owner_name']) ?>
                            <?php if (!$memo['is_private'] && $memo['shared_with']): ?>
                                <br>共有: <?= h($memo['shared_with']) ?>
                            <?php endif; ?>
                        </p>
                        <?php if($is_admin_or_editor): ?>
                            <div class="mt-2">
                                <a href="memo_edit.php?id=<?= $memo['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-2">編集</a>
                                <form action="" method="POST" class="inline">
                                    <input type="hidden" name="delete" value="<?= $memo['id'] ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('本当に削除しますか？');">削除</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>