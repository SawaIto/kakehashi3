<?php
session_start();
include("funcs.php");

if (!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id() || $_SESSION["kanri_flg"] != 1) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];

$pdo = db_conn();
$sql = "SELECT * FROM schedules WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status) {
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    exit("スケジュールの取得に失敗しました。");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST["date"];
    $comment = $_POST["comment"];

    $sql = "UPDATE schedules SET date = :date, comment = :comment WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":date", $date, PDO::PARAM_STR);
    $stmt->bindValue(":comment", $comment, PDO::PARAM_STR);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status) {
        header("Location: select.php");
        exit;
    } else {
        exit("スケジュールの更新に失敗しました。");
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>スケジュール編集</title>
</head>
<body class="bg-blue-100 min-h-screen flex flex-col items-center justify-center">
    <?php include 'header.php'; ?>
    <form action="detail.php?id=<?= $id ?>" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 mt-32 sm:mt-20">
        <div>
            <fieldset>
                <legend class="block text-gray-700 font-bold mb-2">スケジュール編集</legend>
                <div class="mb-4">
                    <label for="date" class="block text-gray-700 font-bold mb-2">日付</label>
                    <input type="date" name="date" id="date" class="w-full border border-gray-300 rounded py-2 px-3" value="<?= $schedule["date"] ?>">
                </div>
                <div class="mb-6">
                    <label for="comment" class="block text-gray-700 font-bold mb-2">コメント</label>
                    <textarea name="comment" id="comment" class="w-full border border-gray-300 rounded py-2 px-3" rows="4"><?= $schedule["comment"] ?></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">更新</button>
                </div>
            </fieldset>
        </div>
    </form>
    <footer class="w-full mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>