<?php
session_start();
include("funcs.php");

// セッションチェック
sschk();

$pdo = db_conn();

// ユーザーリストの取得（現在のユーザーを除く）
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE id != :current_user_id");
$stmt->bindValue(':current_user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// POSTリクエストの処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $comment = $_POST["comment"];
    $user_name = $_SESSION["name"];
    $is_private = isset($_POST['is_private']) ? 1 : 0;
    $shared_users = isset($_POST['shared_users']) ? $_POST['shared_users'] : [];

    // データベース接続
    $pdo = db_conn();

    try {
        $pdo->beginTransaction();

        // スケジュールの登録
        $sql = "INSERT INTO schedules (user_name, date, comment, is_private) VALUES (:user_name, :date, :comment, :is_private)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindValue(":date", $date, PDO::PARAM_STR);
        $stmt->bindValue(":comment", $comment, PDO::PARAM_STR);
        $stmt->bindValue(":is_private", $is_private, PDO::PARAM_BOOL);
        $stmt->execute();

        $schedule_id = $pdo->lastInsertId();

        // 共有情報の登録
        if (!$is_private) {
            foreach ($shared_users as $user_id) {
                $stmt = $pdo->prepare("INSERT INTO schedule_shares (schedule_id, user_id) VALUES (:schedule_id, :user_id)");
                $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        $pdo->commit();
        header("Location: select.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        exit("スケジュールの登録に失敗しました。エラー: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>スケジュール入力</title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col items-center justify-center">
<?php include 'header.php'; ?>

    <!-- スケジュール入力フォーム -->
    <form action="input.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-32 sm:mt-20 w-11/12 md:w-1/2">
        <div>
            <fieldset>
                <legend class="text-lg font-bold mb-4">スケジュール登録</legend>
                <div class="mb-4">
                    <label for="date" class="block text-gray-700 font-bold mb-2">日付:</label>
                    <input type="date" name="date" id="date" class="w-full border border-gray-300 rounded py-2 px-3" required>
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-gray-700 font-bold mb-2">コメント:</label>
                    <textarea name="comment" id="comment" class="w-full border border-gray-300 rounded py-2 px-3" rows="4" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_private" class="form-checkbox">
                        <span class="ml-2">プライベート（共有しない）</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">共有するユーザー:</label>
                    <?php foreach ($users as $user): ?>
                        <label class="inline-flex items-center mt-3">
                            <input type="checkbox" name="shared_users[]" value="<?= $user['id'] ?>" class="form-checkbox">
                            <span class="ml-2"><?= h($user['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center justify-between">
                    <input type="submit" value="保存" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                </div>
            </fieldset>
        </div>
    </form>

    <!-- フッター -->
    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>