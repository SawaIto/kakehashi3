<?php
session_start();
include("funcs.php");
sschk();
$pdo = db_conn();

// 管理者以外はアクセス不可
if ($_SESSION["kanri_flg"] != 1) {
  redirect('select.php');
  exit;
}

$sql = "SELECT * FROM users";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status == false) {
  sql_error($stmt);
}

$values =  $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>

<?php if(isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <?php
        switch($_GET['error']) {
            case '1':
                echo "すべてのフィールドを入力してください。";
                break;
            case '2':
                echo "入力されたIDまたはメールアドレスは既に使用されています。";
                break;
            case '3':
                echo "登録中にエラーが発生しました。もう一度お試しください。";
                break;
            default:
                echo "エラーが発生しました。";
        }
        ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー登録</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<?php include 'header.php'; ?>

<body class="bg-blue-100 min-h-screen flex flex-col items-center justify-center">
<form method="post" action="user_insert.php" class="max-w-md mx-auto mt-32 sm:mt-20 px-10 py-8 mb-4 bg-white rounded-lg shadow-md">
    <fieldset>
        <legend class="text-lg font-bold mb-4">ユーザー登録</legend>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="name">名前：</label>
            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="name" type="text" name="name" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="email">メールアドレス：</label>
            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="email" type="email" name="email" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="lid">Login ID：</label>
            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="lid" type="text" name="lid" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="lpw">Login PW：</label>
            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="lpw" type="password" name="lpw" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2" for="group_id">グループID：</label>
            <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="group_id" type="number" name="group_id" required>
        </div>
            <div class="flex justify-end">
        <input type="submit" value="登録" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
      </div>
    </fieldset>
  </form>
</body>
<footer class="w-full mt-auto">
  <?php include 'footer.php'; ?>
</footer>

</html>