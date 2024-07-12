<?php
include("funcs.php");
sschk();
$pdo = db_conn();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'view') {
    redirect('login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['album_id'])) {
    $album_id = $_POST['album_id'];
    $group_id = $_SESSION['group_id'];

    // アルバムがユーザーのグループに属していることを確認
    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND group_id = ?");
    $stmt->execute([$album_id, $group_id]);
    $album = $stmt->fetch();

    if ($album) {
        // トランザクション開始
        $pdo->beginTransaction();

        try {
            // album_photosテーブルから関連レコードを削除
            $stmt = $pdo->prepare("DELETE FROM album_photos WHERE album_id = ?");
            $stmt->execute([$album_id]);

            // albumsテーブルからアルバムを削除
            $stmt = $pdo->prepare("DELETE FROM albums WHERE id = ?");
            $stmt->execute([$album_id]);

            // トランザクションをコミット
            $pdo->commit();

            $_SESSION['success_message'] = 'アルバムが正常に削除されました。';
        } catch (Exception $e) {
            // エラーが発生した場合はロールバック
            $pdo->rollBack();
            $_SESSION['error_message'] = 'アルバムの削除中にエラーが発生しました。';
        }
    } else {
        $_SESSION['error_message'] = '指定されたアルバムが見つからないか、削除する権限がありません。';
    }
} else {
    $_SESSION['error_message'] = '無効なリクエストです。';
}

redirect('album_view.php');