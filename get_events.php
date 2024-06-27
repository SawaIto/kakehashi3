<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

$user_id = $_SESSION['user_id'];
$start = $_GET['start'];
$end = $_GET['end'];

// ユーザーの権限に基づいてイベントを取得
$sql = "SELECT e.* FROM events e 
        LEFT JOIN event_shares es ON e.id = es.event_id
        WHERE (e.creator_id = :user_id OR es.user_id = :user_id OR e.group_id = :group_id)
        AND e.start_date BETWEEN :start AND :end
        AND (e.is_private = 0 OR e.creator_id = :user_id)";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':group_id', $_SESSION['group_id'], PDO::PARAM_INT);
$stmt->bindParam(':start', $start, PDO::PARAM_STR);
$stmt->bindParam(':end', $end, PDO::PARAM_STR);
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// FullCalendar形式にデータを整形
$output = array();
foreach($events as $event) {
    $output[] = array(
        'id' => $event['id'],
        'title' => $event['title'],
        'start' => $event['start_date'],
        'end' => $event['end_date'],
        'description' => $event['description'],
        'allDay' => false
    );
}

header('Content-Type: application/json');
echo json_encode($output);