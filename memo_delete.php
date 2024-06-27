<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザーの権限チェック
if ($_SESSION['kanri_flg'] != 1 && $_SESSION['modify_flg'] != 1) {
    $_SESSION['error_message'] = "メモを削除する権限がありません。";
    redirect('memo_view.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $pdo->beginTransaction();

        // メモの所有者を確認
        $stmt = $pdo->prepare("SELECT user_id FROM memos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $memo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($memo && $memo['user_id'] == $_SESSION['user_id']) {
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