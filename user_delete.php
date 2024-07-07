<?php

require_once 'funcs.php';
sschk();

$pdo = db_conn();

if ($_SESSION['role'] != 'admin') {
    redirect('home.php');
    exit("管理者のみがユーザーを削除できます。");
}

$user_id = $_GET['id'];

// ユーザーが存在するか確認
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = "指定されたユーザーが見つかりません。";
    redirect('user_list.php');
}

// ユーザーが管理者でないことを確認
if ($user['role'] == 'admin') {
    $_SESSION['error_message'] = "管理者ユーザーは削除できません。";
    redirect('user_list.php');
}

try {
    $pdo->beginTransaction();

    // group_membersテーブルから該当ユーザーのレコードを削除
    $stmt = $pdo->prepare("DELETE FROM group_members WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // usersテーブルから該当ユーザーを削除
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    $_SESSION['success_message'] = "ユーザーが正常に削除されました。";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "ユーザーの削除中にエラーが発生しました: " . $e->getMessage();
}

redirect('user_list.php');