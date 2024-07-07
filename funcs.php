<?php
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//DB接続関数：db_conn()
function db_conn() {
    try {
        $db_name = "mydatabase123";    //データベース名
        $db_id   = "myuser123";      //アカウント名
        $db_pw   = "mypassword123";          //パスワード：XAMPPはパスワード無し or MAMPはパスワード"root"に修正してください。
        $db_host = "162.43.7.131"; //DBホスト
        return new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}

//SQLエラー関数：sql_error($stmt)
function sql_error($stmt) {
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}

//リダイレクト関数: redirect($file_name)
function redirect($file_name) {
    header("Location: ".$file_name);
    exit();
}

//SessionCheck(スケルトン)
function sschk(){
    if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
      exit("Login Error");
    }else{
      session_regenerate_id(true); //SESSION KEYを入れ替える
      $_SESSION["chk_ssid"] = session_id();
    }
}
