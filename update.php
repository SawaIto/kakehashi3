<?php
//PHP:コード記述/修正の流れ
//1. insert.phpの処理をマルっとコピー。
//   POSTデータ受信 → DB接続 → SQL実行 → 前ページへ戻る
//2. $id = POST["id"]を追加
//3. SQL修正
//   "UPDATE テーブル名 SET 変更したいカラムを並べる WHERE 条件"
//   bindValueにも「id」の項目を追加
//4. header関数"Location"を「select.php」に変更
session_start();
$name   = $_POST["name"];
$email  = $_POST["email"];
$age    = $_POST["age"];
$satisfaction = $_POST['satisfaction'];
$naiyou = $_POST["naiyou"];
$id    = $_POST["id"];


//2. DB接続します
//*** function化する！  *****************
include("funcs.php"); //外部ファイル読み込み
sschk();
$pdo = db_conn();


//３．データ登録SQL作成
// 危険な文字を回避するために必ずbindvalueを使うこと
$sql ="UPDATE user_table SET name=:name, email=:email, age=:age, satisfaction=:satisfaction, naiyou=:naiyou WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name',   $name,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':email',  $email,  PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':age',    $age,    PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':satisfaction',    $satisfaction,   PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':naiyou', $naiyou, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':id',    $id,    PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //実行


//４．データ登録処理後
if ($status == false) {
    //*** function化する！*****************
    sql_error($stmt);
} else {
    //*** function化する！*****************
    redirect("select.php");
}

?>

