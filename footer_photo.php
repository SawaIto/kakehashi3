<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>footer_photo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
<footer class="bg-gray-300 text-white py-4 fixed bottom-0 left-0 right-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-end mb-4 space-x-2">
            <a href="album_create.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                アルバム作成          
             </a>
            <a href="album_view.php" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                アルバム一覧          
             </a>
                <a href="photo_upload.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-2 sm:px-4 rounded-lg text-xs sm:text-base transition duration-300">
                写真を追加
            </a>
            <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-2 rounded-lg text-xs sm:text-base transition duration-300">
                ホーム
            </a>
        </div>

        <div id="searchFilter" class="hidden mb-4 bg-gray-200 p-4 rounded-lg">
            <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-end space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex-grow">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">開始日:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= h($start_date) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="flex-grow">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">終了日:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= h($end_date) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">検索</button>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">リセット</a>
                </div>
            </form>
        </div>

        <p class="text-xs text-gray-600 text-center">&copy; Kakehashi2024. All rights reserved.</p>
    </div>
</footer>

<script>
    function toggleSearchFilter() {
        var searchFilter = document.getElementById('searchFilter');
        searchFilter.classList.toggle('hidden');
    }

    function toggleExtraColumns() {
        var extraColumns = document.querySelectorAll('.extra-column');
        extraColumns.forEach(function(column) {
            column.classList.toggle('hidden');
        });
    }
</script>
</body>
</html>