<?php
//1. POSTデータ取得
//[name,email,age,naiyou]
$name = $_POST['name'];
$email = $_POST['email']; 
$age = $_POST['age'];
$satisfaction = $_POST['satisfaction'];
$naiyou = $_POST['naiyou'];


//2. DB接続します
include("funcs.php"); //外部ファイル読み込み
$pdo = db_conn();

//３．データ登録SQL作成
$sql= "INSERT INTO kakehashi(name,email,age,satisfaction,naiyou,indate)VALUES(:name,:email,:age,:satisfaction,:naiyou,sysdate());";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name',   $name,      PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':email',  $email,     PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':age',    $age,       PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':satisfaction', $satisfaction,    PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':naiyou', $naiyou,    PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();//true or false

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}else{
  //５．index.phpへリダイレクト
  redirect("index.php");
}
?>
