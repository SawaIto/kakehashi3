<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

// ユーザーの権限チェック
if ($_SESSION['kanri_flg'] != 1 && $_SESSION['modify_flg'] != 1) {
    redirect('memo_view.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM memos WHERE id = :id AND user_id = :user_id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $status = $stmt->execute();
    
    if($status == false){
        sql_error($stmt);
    } else {
        redirect('memo_edit.php');
    }
} else {
    redirect('memo_edit.php');
}
?>