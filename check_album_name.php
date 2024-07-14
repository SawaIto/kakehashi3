<?php
require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $album_name = $_POST['album_name'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM albums WHERE name = ? AND group_id = ?");
    $stmt->execute([$album_name, $_SESSION['group_id']]);
    $album_exists = $stmt->fetchColumn();

    echo json_encode(['exists' => $album_exists > 0]);
} else {
    echo json_encode(['error' => '不正なリクエストです。']);
}