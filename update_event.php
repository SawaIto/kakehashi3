<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;
    $shared_users = isset($_POST['shared_users']) ? $_POST['shared_users'] : [];

    try {
        $pdo->beginTransaction();

        // イベントの更新
        $stmt = $pdo->prepare("UPDATE events SET title = :title, description = :description, start_date = :start_date, end_date = :end_date, is_private = :is_private, last_modified_by = :last_modified_by, last_modified_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':is_private' => $is_private,
            ':last_modified_by' => $_SESSION['user_id'],
            ':id' => $event_id
        ]);

        // 共有情報の更新
        $stmt = $pdo->prepare("DELETE FROM event_shares WHERE event_id = :event_id");
        $stmt->execute([':event_id' => $event_id]);

        if (!$is_private) {
            foreach ($shared_users as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO event_shares (event_id, user_id) VALUES (:event_id, :user_id)");
                $stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
            }
        }

        $pdo->commit();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error" => $e->getMessage()]);
    }
}