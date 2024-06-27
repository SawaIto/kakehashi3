<?php
session_start();
include("funcs.php");
sschk();

// 管理者以外はアクセス不可
if ($_SESSION["kanri_flg"] != 1) {
    redirect('select.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $lid = filter_input(INPUT_POST, "lid", FILTER_SANITIZE_STRING);
    $lpw = filter_input(INPUT_POST, "lpw", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $kanri_flg = isset($_POST["kanri_flg"]) ? 1 : 0;
    $modify_flg = isset($_POST["modify_flg"]) ? 1 : 0;
    $view_flg = isset($_POST["view_flg"]) ? 1 : 0;

    // 入力チェック
    if (empty($name) || empty($lid) || empty($lpw) || empty($email)) {
        redirect("user.php?error=1"); // すべてのフィールドが必須
        exit;
    }

    $pdo = db_conn();

    try {
        // 重複チェック
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE lid = :lid OR email = :email");
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            redirect("user.php?error=2"); // 重複エラー
            exit;
        }

        $lpw = password_hash($lpw, PASSWORD_DEFAULT); //パスワードハッシュ化

        $sql = "INSERT INTO users (name, lid, lpw, email, kanri_flg, modify_flg, view_flg) VALUES (:name, :lid, :lpw, :email, :kanri_flg, :modify_flg, :view_flg)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
        $stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
        $stmt->bindValue(':modify_flg', $modify_flg, PDO::PARAM_INT);
        $stmt->bindValue(':view_flg', $view_flg, PDO::PARAM_INT);
        $status = $stmt->execute();

        if($status == false){
            sql_error($stmt);
        } else {
            redirect("user_list.php");
        }
    } catch (PDOException $e) {
        // データベースエラーの処理
        redirect("user.php?error=3"); // データベースエラー
        exit;
    }
}
?>