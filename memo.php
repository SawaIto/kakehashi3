<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>メモ帳</title>
  <!-- Tailwind CSS の CDN リンクを追加 -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
  <div class="container mx-auto pt-3 pb-6 flex flex-col items-center">
    <div class="flex items-center mb-4">
      <img src="./img/cork_board_paper6.png" alt="" class="w-8 h-8 mr-4">
      <h2 class="text-2xl font-bold">メモ帳</h2>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full mb-8">
      <div class="lg:flex flex-col">
        <!-- メモの入力フォーム -->
        <div class="bg-white rounded-lg p-4">
          <h3 class="text-xl font-bold mb-4 bg-yellow-200 text-center rounded px-2 py-1">メモしたい内容を入力</h3>
          <label for="category" class="block font-bold mb-2">カテゴリー:</label>
          <select id="categorySelect" class="border border-gray-300 rounded-md w-full py-2 px-3 mb-3">
            <option value="" disabled selected>選択してください</option>
            <option value="買い物">買い物</option>
            <option value="予定">予定</option>
            <option value="その他">その他</option>
          </select>
          <label for="memoTitle" class="block font-bold mb-2">表題:</label>
          <input type="text" id="memoTitle" class="border border-gray-300 rounded-md w-full py-2 px-3 mb-3">
          <label for="memoContent" class="block font-bold mb-2">内容:</label>
          <textarea id="memoContent" class="border border-gray-300 rounded-md w-full py-2 px-3 mb-3"
            rows="4"></textarea>

          <button onclick="saveMemo()"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">保存する</button>
          <button onclick="clearMemo()"
            class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded">入力内容を消去</button>
          <br>
        </div>
      </div>
      <div class="lg:flex flex-col">
        <!-- メモリスト -->
        <div class="bg-white rounded-lg p-4">
          <h3 class="text-xl font-bold mb-4 bg-yellow-200 text-center rounded px-2 py-1">メモリスト</h3>
          <div id="memoLists"></div>
        </div>
      </div>
      <div class="lg:flex flex-col">
        <!-- メモ表示エリア -->
        <div id="displayMemo" class="bg-white rounded-lg p-4 mb-4">
          <h3 class="text-xl font-bold mb-2 bg-yellow-200 rounded text-center px-2 py-1">メモの内容</h3>
          <div id="memoContentDisplay" class="bg-green-100 rounded px-5 py-10">
            <p><strong>カテゴリー:</strong> <span id="memoCategoryDisplay"></span></p>
            <p><strong>表題:</strong> <span id="memoTitleDisplay"></span></p>
            <p><strong>内容:</strong> <span id="memoContentText"></span></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // メモ内容を保存する
    function saveMemo() {
      var category = document.getElementById("categorySelect").value;
      var title = document.getElementById("memoTitle").value;
      var content = document.getElementById("memoContent").value;

      // 指示の部分: カテゴリー選択、表題、内容の3つが必須であり、それらがそろった場合のみ保存する
      if (category === "" || title === "" || content === "") {
        // ポップアップメッセージを作成
        var popup = document.createElement("div");
        popup.textContent = "カテゴリー選択、表題、内容は必須です。";
        popup.classList.add("fixed", "top-1/2", "left-1/2", "transform", "-translate-x-1/2", "-translate-y-1/2", "bg-gray-500", "rounded-lg", "p-8", "shadow-lg", "text-white", "text-center");

        // ポップアップメッセージを画面に追加
        document.body.appendChild(popup);

        // 3秒後にポップアップメッセージを削除
        setTimeout(function () {
          popup.remove();
        }, 3000);
        return;
      }

      var memos = JSON.parse(localStorage.getItem(category)) || [];
      if (memos.length >= 3) {
        // ポップアップメッセージを作成
        var popup = document.createElement("div");
        popup.textContent = "各カテゴリーごとに3つまでのメモ保存が可能です。不要なメモを削除してください。";
        popup.classList.add("fixed", "top-1/2", "left-1/2", "transform", "-translate-x-1/2", "-translate-y-1/2", "bg-Gray-500", "rounded-lg", "p-8", "shadow-lg", "text-white", "text-center");

        // ポップアップメッセージを画面に追加
        document.body.appendChild(popup);

        // 3秒後にポップアップメッセージを削除
        setTimeout(function () {
          popup.remove();
        }, 3000);

        return;

      }
      memos.push({ title: title, content: content });
      localStorage.setItem(category, JSON.stringify(memos));

      refreshMemoList();
    }

    function clearMemo() {
      document.getElementById("memoTitle").value = ""; // 表題の入力内容をクリア
      document.getElementById("memoContent").value = ""; // 内容の入力内容をクリア
    }

    function deleteMemo(category, index) {
      var memos = JSON.parse(localStorage.getItem(category)) || [];
      memos.splice(index, 1);
      localStorage.setItem(category, JSON.stringify(memos));
      refreshMemoList();
      displayMemo('', ''); // メモの内容の表示を空にする
    }

    function refreshMemoList() {
      var memoLists = document.getElementById("memoLists");
      memoLists.innerHTML = "";

      ["買い物", "予定", "その他"].forEach(function (category) {
        var memos = JSON.parse(localStorage.getItem(category)) || [];

        var memoListContainer = document.createElement("div");
        memoListContainer.classList.add("mb-4", "flex", "justify-between", "items-center");

        var memoListTitle = document.createElement("h4");
        memoListTitle.textContent = category;
        memoListTitle.classList.add("font-semibold", "mb-2");
        memoListContainer.appendChild(memoListTitle);

        var deleteButton = document.createElement("button");
        deleteButton.textContent = "選択したメモを消去";
        deleteButton.classList.add("bg-red-500", "hover:bg-red-600", "text-white", "font-bold", "py-2",
          "px-4", "rounded", "mt-2");
        deleteButton.addEventListener("click", function () {
          var memoList = document.getElementById(category + "-memoList"); // メモリストのIDを取得
          var selectedIndex = memoList.value;
          deleteMemo(category, selectedIndex);
        });
        memoListContainer.appendChild(deleteButton);

        memoLists.appendChild(memoListContainer);

        var memoList = document.createElement("select");
        memoList.id = category + "-memoList"; // メモリストのIDを設定
        memoList.classList.add("border", "border-gray-300", "rounded-md", "w-full", "py-2", "px-3", "mb-3");
        memoList.setAttribute("size", "3");
        memoList.addEventListener("change", function () {
          displayMemo(category, memoList.value);
        });

        memos.forEach(function (memo, index) {
          var option = document.createElement("option");
          option.value = index;
          option.text = memo.title;
          memoList.appendChild(option);
        });

        memoLists.appendChild(memoList);
      });
    }

    function displayMemo(category, index) {
      var memos = JSON.parse(localStorage.getItem(category)) || [];
      var memo = memos[index];
      var memoContentDisplay = document.getElementById("memoContentDisplay");
      memoContentDisplay.innerHTML = "<p><strong>カテゴリー:</strong> " + category + "</p>" +
        "<p><strong>表題:</strong> " + (memo ? memo.title : '') + "</p>" +
        "<p><strong>内容:</strong> " + (memo ? memo.content : '') + "</p>";
    }

    // ページロード時にメモリストを更新する
    refreshMemoList();
  </script>

</body>
</html>