<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <footer class="text-sm bg-gray-300 text-white py-4 text-center fixed bottom-0 left-0 right-0 z-50">
        <div class="flex justify-end mb-4 sm:mb-6 mt-2">
            <div class="flex space-x-2">
                <a href="home.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                    ホーム
                </a>
                <button onclick="toggleSearchFilter()" class="bg-blue-400 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                    検索
                </button>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'modify') : ?>
                <button onclick="toggleExtraColumns()" class="bg-purple-400 hover:bg-purple-500 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                    詳細
                </button>
                    <a href="memo_input.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">
                        新規登録
                    </a>
                    <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])) : ?>
            <p class="text-sm sm:text-base text-green-500 mb-4 text-center"><?= h($_SESSION['success_message']) ?></p>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])) : ?>
            <p class="text-sm sm:text-base text-red-500 mb-4 text-center"><?= h($_SESSION['error_message']) ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- 検索とフィルタリングフォーム -->
        <div id="searchFilter" class="hidden mb-4 bg-gray-100 p-4 rounded-lg">
            <form action="" method="GET" class="flex flex-col sm:flex-row sm:items-end space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex-grow text-black">
                    <input type="text" name="search" placeholder="検索..." value="<?= h($search) ?>" class="w-full p-2 border rounded">
                </div>
                <div class="flex-grow">
                    <select name="category" class="w-full p-2 border rounded text-black">
                        <option value="">カテゴリー選択</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?= h($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">検索</button>
                    <a href="memo_view.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm sm:text-base transition duration-300">リセット</a>
                </div>
            </form>
        </div>
        <p class="text-black">&copy; 2024 sawasawasawa. All rights reserved.</p>
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