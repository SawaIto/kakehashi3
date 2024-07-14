<?
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// ページでユーザがメールアドレスを入力すればユーザ特定できる
$to = "yuichiclimber@gmail.com";
$title = "パスワード再設定の手続き";
$content = "下記のURLにアクセスして再発行手続きを行なってください。\r\n";
// ユーザ特定できたならそのメールアドレスのユーザIDを取得するSQL

$password_link = "https://test.com?user_id={$user_id}";
$content .= $password_link;

if(mb_send_mail($to, $title, $content)){
	echo "メールを送信しました";
} else {
	echo "メールの送信に失敗しました";
}
?>
