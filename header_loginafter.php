<!DOCTYPE html>
<html lang="ja">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<header class="h-26  fixed top-0 left-0 mb-5 w-full bg-white shadow-md z-50 flex flex-col sm:flex-row justify-between items-center px-5 py-2">
    <a href="http://sakura110.sakura.ne.jp/kadai09_php/index.php" class="flex title-font font-medium items-center text-gray-900 mb-2 sm:mb-0">
        <img src="./img/header_logo.jpg" alt="ひまわりのロゴ"  style="width: 105px; height: 60px;" class="mr-4">

    </a>
    <nav class="flex flex-col sm:flex-row">
        <ul class="flex justify-end items-center space-x-6">
        <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a href="select.php">データ一覧</a></li>
        <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a onclick="scrollToComment()">コメント</a></li>
            <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a href="select.php">アンケート結果</a></li>
            <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a href="#COURSE">読み込む</a></li>
            <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a href="#NEWS">コード</a></li>
            <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a href="#ACCESS">作成</a></li>
            <li class="text-sm md:text-base lg:text-lg xl:text-xl hover:text-white hover:bg-yellow-500"><a href="#CONTACT">練習</a></li>
        </ul>
    </nav>
</header>

<body>

<script>
    function scrollToComment() {
        // Get the target element
        const commentSection = document.querySelector('.h-32');

        // Scroll to the target element
        commentSection.scrollIntoView({ behavior: 'smooth' });
    }
</script>

</body>

</html>