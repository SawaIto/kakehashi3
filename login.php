<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>ログイン</title>
    <script>
        function showPopup(message) {
            const popup = document.createElement('div');
            popup.className = 'fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50';
            popup.innerHTML = `
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <p class="text-xl">${message}</p>
                <button onclick="this.closest('.fixed').remove()" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    閉じる
                </button>
            </div>
        `;
            document.body.appendChild(popup);
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('registration') === 'success') {
                showPopup('管理者登録が完了しました。ログインしてください。');
            }
        };
    </script>
</head>

<body class="bg-blue-100 min-h-screen flex flex-col">
    <?php include 'header0.php'; ?>

    <?php
    if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
        echo '<script>showPopup("管理者登録が完了しました。ログインしてください。");</script>';
    }
    ?>

    <main class="flex-grow container mx-auto px-4 py-8 flex items-center justify-center">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">ログイン</h2>
            <form action="login_act.php" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="username">ID:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="username" type="text" name="lid" placeholder="ID">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-xl font-bold mb-2" for="password">PW:</label>
                    <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 text-xl focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" id="password" type="password" name="lpw" placeholder="Password">
                </div>
                <div class="flex items-center justify-between">
                    <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-xl py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="ログイン">
                </div>
            </form>
            <div class="mt-4">
                <a href="password_reset.php" class="text-center font-bold hover:text-blue-700 text-lg">パスワードを忘れた方はこちら</a>
            </div>
        </div>
    </main>

    <footer class="w-full bg-white shadow-md mt-auto">
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>