<?php
session_start();
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'modify') {
    redirect('home.php');
    exit("スケジュールの削除権限がありません。");
}

$schedule_id = $_GET['id'];

// スケジュールの存在確認
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? AND group_id = ?");
$stmt->execute([$schedule_id, $_SESSION['group_id']]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$schedule) {
    $_SESSION['error_message'] = "指定されたスケジュールが見つかりません。";
    redirect('schedule_view.php');
    exit();
}

try {
    $pdo->beginTransaction();

    // スケジュール削除
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ? AND group_id = ?");
    $stmt->execute([$schedule_id, $_SESSION['group_id']]);

    // 関連する共有情報も削除
    $stmt = $pdo->prepare("DELETE FROM schedule_shares WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);

    $pdo->commit();

    $_SESSION['success_message'] = "スケジュールが正常に削除されました。";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "スケジュールの削除中にエラーが発生しました: " . $e->getMessage();
}

redirect('schedule_view.php');
exit();
?>