<?php

// config.phpを読み込む
$config = require __DIR__ . '/config.php';

// セッション名を設定（セッション開始前に行う）
if (isset($config['session']['name'])) {
    session_name($config['session']['name']);
}

session_start();


//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//DB接続関数：db_conn()
function db_conn() {
    global $config;
    try {
        $db = $config['db'];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
        $pdo = new PDO($dsn, $db['user'], $db['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}

//SQLエラー関数：sql_error($stmt)
function sql_error($stmt) {
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}

//リダイレクト関数: redirect($file_name)
function redirect($file_name) {
    header("Location: ".$file_name);
    exit();
}

//SessionCheck
function sschk() {
    if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()){
        exit("Login Error");
    } else {
        session_regenerate_id(true); //SESSION KEYを入れ替える
        $_SESSION["chk_ssid"] = session_id();
    }
}

// メール送信関数
function send_mail($to, $subject, $body) {
    global $config;
    
    require_once 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config['mail']['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['mail']['username'];
        $mail->Password   = $config['mail']['password'];
        $mail->SMTPSecure = $config['mail']['encryption'];
        $mail->Port       = $config['mail']['port'];

        //Recipients
        $mail->setFrom($config['mail']['from_address'], $config['mail']['from_name']);
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// ログ関数
function log_message($message) {
    global $config;
    $log_file = $config['app']['log_file'] ?? 'app.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}