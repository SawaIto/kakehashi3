<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<footer class="bg-gray-300 text-white py-4 text-center fixed bottom-0 left-0 right-0 z-50">
    <div class="flex flex-col sm:flex-row justify-between items-start mr-2 sm:items-center mb-4 sm:mb-6">
                <div class="flex justify-end space-x-2 w-full sm:w-auto">
                <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                    ホーム
                </a>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                    <a href="schedule_input.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 sm:px-4 rounded-lg text-sm sm:text-base transition duration-300">
                        新規登録
                    </a>
                <?php endif; ?>
                <button onclick="toggleSearchFilter()" class="bg-blue-400 hover:bg-blue-500 text-white font-bold py-2 px-3 sm:px-4 rounded-lg text-sm sm:text-base transition duration-300">
                    検索
                </button>
                <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-white font-bold py-2 px-3 sm:px-4 rounded-lg text-sm sm:text-base transition duration-300">
                    詳細表示
                </button>
            </div>
        </div>

        <div id="searchFilter" class="hidden mb-4 bg-gray-100 p-4 rounded-lg">
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

        <p class="text-sm text-gray-600">&copy; 2024 sawasawasawa. All rights reserved.</p>
    </div>
</footer>
</body>
</html>