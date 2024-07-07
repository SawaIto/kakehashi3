<?php
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザーの権限チェック
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    $_SESSION['error_message'] = "メモを削除する権限がありません。";
    redirect('memo_view.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $pdo->beginTransaction();

        // メモの所有者または共有されているユーザーかを確認
        $stmt = $pdo->prepare("
            SELECT m.user_id, ms.user_id AS shared_user_id, m.group_id
            FROM memos m
            LEFT JOIN memo_shares ms ON m.id = ms.memo_id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        $memo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($memo && ($memo['user_id'] == $_SESSION['user_id'] || $memo['shared_user_id'] == $_SESSION['user_id'] || $memo['group_id'] == $_SESSION['group_id'])) {
            // 共有情報の削除
            $stmt = $pdo->prepare("DELETE FROM memo_shares WHERE memo_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // メモの削除
            $stmt = $pdo->prepare("DELETE FROM memos WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();
            $_SESSION['success_message'] = "メモが正常に削除されました。";
        } else {
            throw new Exception("メモが見つからないか、削除する権限がありません。");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "メモの削除中にエラーが発生しました: " . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = "削除するメモが指定されていません。";
}

redirect('memo_view.php');
?>