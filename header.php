<!DOCTYPE html>
<html lang="ja">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="styles/main.css">
<header class="h-26 fixed top-0 left-0 mb-5 w-full bg-white shadow-md z-50 flex flex-col sm:flex-row justify-between items-center px-5 py-2">
    <div class="flex items-center">
        <a class="flex title-font font-medium items-center text-gray-900 mb-2 sm:mb-0">
            <img src="./img/header_logo.png" alt="橋のロゴ" style="width: 90px; height: 60px;" class="mr-4">
        </a>
    </div>
    <div class="container-fluid">
    <nav class="flex flex-col sm:flex-row">
        <ul class="flex justify-end items-center space-x-6">
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="about_kakehashi.php">かけ橋について</a></li>
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="howtouse.php">使い方</a></li>
            <li class="text-xs sm:text-base hover:text-white hover:bg-yellow-500"><a href="login.php">ログイン</a></li>
        </ul>
</div>
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
