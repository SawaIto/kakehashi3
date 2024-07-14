<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo_id = $_POST['photo_id'];
    $comment = $_POST['comment'];

    $stmt = $pdo->prepare("UPDATE photos SET comment = ? WHERE id = ? AND group_id = ?");
    $status = $stmt->execute([$comment, $photo_id, $_SESSION['group_id']]);

    if ($status) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'コメントの更新に失敗しました。']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '不正なリクエストです。']);
}