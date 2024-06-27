<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .hidden {
      display: none;
    }
  </style>
  <script>
    function autofillAndSubmit() {
      const form = document.getElementById('login-form');
      const usernameInput = form.querySelector('#username');
      const passwordInput = form.querySelector('#password');

      usernameInput.value = 'masae';
      passwordInput.value = 'masae';

      form.submit();
    }
  </script>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center">
  <!-- ログインフォーム -->
  <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="flex justify-center">
      <button class="bg-blue-700 hover:bg-green-700 text-white font-bold text-4xl py-5 px-6 rounded mb-4" onclick="autofillAndSubmit()">正恵さん、俊郎さん、悦子さんが<br>はじめるために押す</button>
    </div>
    <form id="login-form" action="login_act.php" method="post" class="hidden">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2 text-xl" for="username">ID:</label>
        <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" id="username" type="text" name="lid" placeholder="ID">
      </div>
      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2 text-xl" for="password">PW:</label>
        <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" id="password" type="password" name="lpw" placeholder="Password">
      </div>
      <div class="flex items-center justify-between">
        <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hidden" type="submit" value="ログイン">
      </div>
    </form>
  </div>
</body>
</html>




<!-- <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function autofillAndSubmit() {
            document.getElementById('username').value = 'test2';
            document.getElementById('password').value = 'test2';
            document.forms[0].submit();
        }
    </script>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center">
    <!-- ログインフォーム -->
    <!-- lLOGINogin_act.php は認証処理用のPHPです。 -->
    <!-- <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="flex justify-center">
  <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-4xl py-5 px-6 rounded mb-4" onclick="autofillAndSubmit()">正恵さんが<br>はじめるために押す</button>
</div>
        <form name="form1" action="login_act.php" method="post" class="">
            <p class="text-center m-5">正恵さん以外の方は以下にIDとPWを入れて送信ボタンを押してください。</p>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2 text-xl" for="username">ID:</label>
                <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" id="username" type="text" name="lid" placeholder="ID">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2 text-xl" for="password">PW:</label>
                <input class="border border-blue-500 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" id="password" type="password" name="lpw" placeholder="Password">
            </div>
            <div class="flex items-center justify-between">
                <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="ログイン">
            </div>
        </form>
    </div>
</body>
</html> -->
