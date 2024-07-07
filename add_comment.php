<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo_id = $_POST['photo_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    if (empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'コメントを入力してください。']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO photo_comments (photo_id, user_id, comment) VALUES (?, ?, ?)");
    $status = $stmt->execute([$photo_id, $user_id, $comment]);

    if ($status) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'コメントの追加に失敗しました。']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '不正なリクエストです。']);
}