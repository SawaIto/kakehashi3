<?php
require_once 'funcs.php';
sschk();
$pdo = db_conn();
$current_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$query = "SELECT * FROM chats ORDER BY is_top_flag DESC, created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['is_top_flag'])) {
    $chat_id = $_POST['chat_id']; // ボタンが押されたチャットのIDを取得

    // すべてのチャットの is_top_flag を 0 にリセット
    $reset_stmt = $pdo->prepare("UPDATE chats SET is_top_flag = 0");
    $reset_stmt->execute();

    // 現在のチャットの is_top_flag を 1 に設定
    $stmt = $pdo->prepare("UPDATE chats SET is_top_flag = 1 WHERE id = ?");
    $stmt->execute([$chat_id]);

    // ページをリロードして変更を反映
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

function getUsernameById($userid,$pdo){
    // ユーザーIDに基づいてユーザー名を取得するクエリ
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userid]);
    // 結果を取得
    $user = $stmt->fetch();
    // ユーザー名を返す。ユーザーが見つからない場合はnullを返す
    return $user ? $user['username'] : null;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>チャット一覧</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="styles/main.css">
<script>
function toggleSearchFilter() {
    var searchFilter = document.getElementById('searchFilter');
    searchFilter.classList.toggle('hidden');
}
function toggleExtraColumns() {
    var extraColumns = document.querySelectorAll('.extra-column');
    var isHidden = extraColumns[0].classList.contains('hidden');
    extraColumns.forEach(function(column) {
        column.classList.toggle('hidden');
    });
    // 状態をローカルストレージに保存
    localStorage.setItem('extraColumnsVisible', isHidden ? 'true' : 'false');
}
// ページ読み込み時に実行する関数
function loadExtraColumnsState() {
    var isVisible = localStorage.getItem('extraColumnsVisible') === 'true';
    var extraColumns = document.querySelectorAll('.extra-column');
    extraColumns.forEach(function(column) {
        if (isVisible) {
            column.classList.remove('hidden');
        } else {
            column.classList.add('hidden');
        }
    });
}

// ページ読み込み時に状態を復元
document.addEventListener('DOMContentLoaded', loadExtraColumnsState);
</script>
<style>
.content-wrapper {height: calc(100vh -20px);overflow-y: auto;}
.overflow-x-auto.schedule_table table td:first-of-type {width: 70px;}
.meta-info {font-size: 0.5rem;color: #666;}
.schedule-owner {font-weight: bold;text-align: left;margin-bottom: 0.25rem;}
.schedule-content {text-align: left;}
td.schedule-cell {padding: 0.5rem;}
</style>
</head>

<body class="bg-blue-50" id="body">
    <?include 'header0.php'; ?>
    <div class="pt-16 sm:pt-20">
        <main class="container mx-auto mt-5 mb-40">
            <div class="bg-white rounded shadow-md p-1 sm:p-6">
                <div class="text-center mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold">チャット表示</h1>
                </div>
                <div id="searchFilter" class="mb-4 hidden">
                    <form method="GET" class="flex space-x-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">開始日</label>
                            <input type="date" id="start_date" name="start_date" value="<?= h($start_date) ?>" class="mt-1 block w-full py-2 py-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">終了日</label>
                            <input type="date" id="end_date" name="end_date" value="<?= h($end_date) ?>" class="mt-1 block w-full py-2 py-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">検索</button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto schedule_table">
                    <div class="inline-block w-full py-1 px-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full border-collapse border border-blue-200">
                            <thead>
                                <tr class="bg-blue-100">
                                    <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 text-center">内容</th>
                                    <?if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                        <th class="text-left text-xs sm:text-sm font-semibold p-2 border border-blue-200 extra-column hidden text-center w-16 sm:w-24">操作</th>
                                    <?endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?foreach ($chats as $chat) : ?>
                                    <?
                                    $chat_members = json_decode($chat['chat_member']);
                                    $style = "";
                                    if(!empty($chat["is_top_flag"])){
                                        $style = "#ffdbdb";
                                    }
                                    ?>
                                    <tr class="hover:bg-blue-50 transition-colors duration-200" style="background:<?=$style?>">
                                        <td class="text-xs sm:text-sm border border-blue-200 schedule-cell">
                                            <div class="schedule-owner"><?=nl2br(h($chat['chat_name'])) ?></div>
                                            <div class="">
                                                <div class="">
                                                    <?
                                                    foreach($chat_members as $member){
                                                        echo getUsernameById($member,$pdo).',';
                                                    }
                                                    ?>
                                                </div>
                                                <a href="./chat_room.php?chat_id=<?=$chat['id']?>">トークへ</a>

                                                <?if(empty($style)){?>
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="chat_id" value="<?=$chat['id']?>">
                                                    <button type="submit" name="is_top_flag" class="bg-blue-500 text-white py-1 px-3 rounded">
                                                        上部へ
                                                    </button>
                                                </form>
                                                <?}?>
                                            </div>
                                            <?if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                                <div class="meta-info extra-column hidden text-end">
                                                    <?if ($chat['updated_at'] != $chat['created_at']) : ?>
                                                        編集者: <?= h($chat['updated_by']) ?><br>
                                                        編集日: <?= date('Y-m-d H:i', strtotime($chat['updated_at'])) ?><br>
                                                    <?else : ?>
                                                        編集者: <?= h($chat['updated_by']) ?><br>
                                                        作成日: <?= date('Y-m-d H:i', strtotime($chat['created_at'])) ?><br>
                                                    <?endif; ?>
                                                </div>
                                            <?endif; ?>

                                        </td>
                                        <?if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                                            <td class="text-xs sm:text-base md:text-lg p-2 border border-blue-200 extra-column hidden">
                                                <div class="flex flex-col space-y-2">
                                                    <a href="<?='schedule_edit.php?id=' . $chat['id']; ?>" href="schedule_edit.php?id=<?= $chat['id'] ?>" class="text-center py-1 px-2 bg-blue-500 text-white hover:bg-blue-700 rounded" onclick="localStorage.setItem('extraColumnsVisible', 'true'); return true;">編集</a>
                                                    <a href="schedule_delete.php?id=<?= $chat['id'] ?>" class="text-center py-1 px-2 bg-red-500 text-white hover:bg-red-700 rounded" onclick="return confirm('本当に削除しますか？') && localStorage.setItem('extraColumnsVisible', 'true');">削除</a>
                                                </div>
                                            </td>
                                        <?endif; ?>
                                    </tr>
                                <?endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end mb-4 mt-10 space-x-2">
                <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">ホーム</a>
                    <?if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                        <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-white font-bold py-2 px-4 sm:px-4 rounded text-xs sm:text-base transition duration-300">
                            詳細表示/非表示
                        </button>
                        <a href="schedule_input.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-xs sm:text-base transition duration-300">
                            予定登録
                        </a>
                    <?endif; ?>
                </div>
                <?if (isset($_SESSION['schedule_message'])) : ?>
                    <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['schedule_message']) ?></p>
                    <?unset($_SESSION['schedule_message']); ?>
                <?endif; ?>

                <?if (isset($_SESSION['error_message'])) : ?>
                    <p class="text-sm sm:text-base text-red-500 mb-4 text-center"><?= h($_SESSION['error_message']) ?></p>
                    <?unset($_SESSION['error_message']); ?>
                <?endif; ?>
            </div>
        </main>
    </div>
    <p class="text-sm text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>
</body>
</html>
