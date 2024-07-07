<?php
include("funcs.php");
sschk();
$pdo = db_conn();

$user_id = $_SESSION['user_id'];
$room_id = $_GET['room_id'];
$last_id = $_GET['last_id'];

$stmt = $pdo->prepare("SELECT cm.*, u.username FROM chat_messages cm
                       JOIN users u ON cm.user_id = u.id
                       WHERE cm.room_id = ? AND cm.id > ?
                       ORDER BY cm.created_at ASC");
$stmt->execute([$room_id, $last_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $message):
?>
    <div class="chat-bubble <?= $message['user_id'] == $user_id ? 'self' : 'other' ?>" data-id="<?= $message['id'] ?>">
        <p class="font-bold"><?= h($message['username']) ?>:</p>
        <p><?= h($message['message']) ?></p>
        <?php if ($message['file_path']): ?>
            <a href="<?= h($message['file_path']) ?>" target="_blank" class="text-blue-500 hover:underline">添付ファイルを表示</a>
        <?php endif; ?>
        <p class="text-xs text-gray-500"><?= h($message['created_at']) ?></p>
    </div>
<?php
endforeach;
?>