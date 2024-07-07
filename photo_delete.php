<?php
require_once 'funcs.php';
sschk();

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'modify') {
    echo json_encode(['success' => false, 'message' => '権限がありません。']);
    exit;
}

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo_id = $_POST['photo_id'];

    // 写真の情報を取得
    $stmt = $pdo->prepare("SELECT file_name FROM photos WHERE id = ? AND group_id = ?");
    $stmt->execute([$photo_id, $_SESSION['group_id']]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($photo) {
        // ファイルを削除
        $file_path = __DIR__ . '/uploads/' . $photo['file_name'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // データベースから削除
        $stmt = $pdo->prepare("DELETE FROM photos WHERE id = ?");
        $status = $stmt->execute([$photo_id]);

        if ($status) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'データベースからの削除に失敗しました。']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '写真が見つかりません。']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '不正なリクエストです。']);
}