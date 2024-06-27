<!DOCTYPE html>
<html lang="ja">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<header class="h-26 fixed top-0 left-0 mb-5 w-full bg-white shadow-md z-50 flex flex-col sm:flex-row justify-between items-center px-5 py-2">
    <div class="flex items-center">
        <a class="flex title-font font-medium items-center text-gray-900 mb-2 sm:mb-0">
            <img src="./img/header_logo.png" alt="橋のロゴ" style="width: 90px; height: 60px;" class="mr-4">
        </a>
        <span class="text-base text-gray-900"><?=$_SESSION["name"]?>さん、<br>こんにちは！</span>
    </div>
    <div class="container-fluid">
    <nav class="flex flex-col sm:flex-row">
        <ul class="flex justify-end items-center space-x-6">
            <?php if ($_SESSION["kanri_flg"] == 1 || $_SESSION["modify_flg"] == 1) : ?>
                <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="input.php">スケジュール登録</a></li>
                <?php endif; ?>
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="select.php">スケジュール一覧</a></li>
            <?php if ($_SESSION["kanri_flg"] == 1) : ?>
                <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="user.php">ユーザー登録</a></li>
                <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="user_list.php">ユーザー一覧</a></li>
            <?php endif; ?>
            <?php if ($_SESSION["kanri_flg"] == 1 || $_SESSION["modify_flg"] == 1) : ?>
                <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="memo_edit.php">メモ新規登録</a></li>
            <?php endif; ?>
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="memo_view.php">メモ表示</a></li>
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="profile.php">プロフィール</a></li>
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="logout.php">ログアウト</a></li>
        </ul>
    </nav>
</div>
</header>

<body>
あいうえお
<script>
    function scrollToComment() {
        // Get the target element
        const commentSection = document.querySelector('.h-32');

        // Scroll to the target element
        commentSection.scrollIntoView({ behavior: 'smooth' });
    }
</script>

</body>