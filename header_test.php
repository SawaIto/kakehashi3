<style>
header {
    background-color: #f8f9fa;
    padding: 10px 0;
    border-bottom: 1px solid #e7e7e7;
}
header h1 {
    text-align: center;
    margin: 0;
}
nav {
    text-align: center;
    margin: 10px 0;
}
nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
nav ul li {
    display: inline;
    margin: 0 10px;
}
nav ul li a {
    text-decoration: none;
    color: #007bff;
}
nav ul li a:hover {
    text-decoration: underline;
}

/* ハンバーガーメニュースタイル */
.hamburger-menu {
    display: none;
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;

}
.hamburger-menu ul {
    list-style-type: none;
    padding: 0;
}
.hamburger-menu ul li {
    margin: 20px 0;
}
.hamburger-menu ul li a {
    text-decoration: none;
    color: #fff;
    font-size: 24px;
}
.hamburger-icon {
    display: none;
    cursor: pointer;
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 24px;
    color: #007bff;
    z-index: 1000;
}
.hamburger-menu.active {
    display: flex;
}
/* @media (max-width: 768px) { */
    nav ul {
        /* display: none; */
    }
    .hamburger-icon {
        display: block;
    }
/* } */
</style>

<header>
    <h1>テストヘッダー</h1>
    <nav>
        <ul>
            <li><a href="user_register.php">ユーザー登録</a></li>
            <li><a href="user_list.php">ユーザー一覧</a></li>
            <li><a href="schedule_input.php">スケジュール登録</a></li>
            <li><a href="schedule_view.php">スケジュール表示</a></li>
            <li><a href="memo_input.php">メモ登録</a></li>
            <li><a href="memo_view.php">メモ表示</a></li>
            <li><a href="photo_upload.php">写真アップロード</a></li>
            <li><a href="photo_view.php">写真表示</a></li>
        </ul>
    </nav>
</header>
<div class="hamburger-icon" onclick="toggleMenu()">&#9776;</div>
<div class="hamburger-menu" id="hamburgerMenu">
    <ul>
        <li><a href="user_register.php">ユーザー登録</a></li>
        <li><a href="user_list.php">ユーザー一覧</a></li>
        <li><a href="schedule_input.php">スケジュール登録</a></li>
        <li><a href="schedule_view.php">スケジュール表示</a></li>
        <li><a href="memo_input.php">メモ登録</a></li>
        <li><a href="memo_view.php">メモ表示</a></li>
        <li><a href="photo_upload.php">写真アップロード</a></li>
        <li><a href="photo_view.php">写真表示</a></li>
    </ul>
</div>
<script>
    function toggleMenu() {
        var menu = document.getElementById('hamburgerMenu');
        menu.classList.toggle('active');
    }

    // メニューの外側をクリックして閉じる
    window.onclick = function(event) {
        var menu = document.getElementById('hamburgerMenu');
        var icon = document.querySelector('.hamburger-icon');
        if (event.target !== menu && event.target !== icon && !menu.contains(event.target)) {
            menu.classList.remove('active');
        }
    }
</script>
