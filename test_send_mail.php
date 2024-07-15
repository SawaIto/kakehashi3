<?
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// ページでユーザがメールアドレスを入力すればユーザ特定できる
$to = "sawa110291@gmail.com";
$title = "パスワード再設定の手続き";
$content = "下記のURLにアクセスして再発行手続きを行なってください。\r\n下記のURLにアクセスして再発行手続きを行なってください。\r\n下記のURLにアクセスして再発行手続きを行なってください。\r\n";
// ユーザ特定できたならそのメールアドレスのユーザIDを取得するSQL

$password_link = "https://google.com";
$content .= $password_link;
$headers = "From: info@sakura110.sakura.ne.jp";

if(mb_send_mail($to, $title, $content, $headers)){
	echo "メールを送信しました";
} else {
	echo "メールの送信に失敗しました";
}
?>