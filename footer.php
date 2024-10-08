<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フッター</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        @media (max-width: 640px) {
            .footer-links {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }
            .footer-links a {
                margin: 0.5rem;
                font-size: 0.8rem;
            }
            .copyright {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- ここにログインフォームのコンテンツを配置 -->

    <footer class="bg-gray-300 py-1 mt-auto fixed bottom-0 left-0 right-0 z-50">
        <div class="container mx-auto px-2">
            <div class="footer-links flex justify-center items-center mb-1 space-x-2">
                <a href="privacy_policy.php" class="text-black hover:underline">
                    プライバシーポリシー
                </a>
                <a href="terms_of_service.php" class="text-black hover:underline">
                    利用規約
                </a>
                <a href="contact_form_pre_login.php" class="text-black hover:underline">
                    お問い合わせ
                </a>
            </div>
            <p class="copyright text-xs text-gray-600 text-center">&copy; Kakehashi2024 All rights reserved.</p>
        </div>
    </footer>
    <script>
    function toggleExtraColumns() {
        var extraColumns = document.querySelectorAll('.extra-column');
        extraColumns.forEach(function(column) {
            column.classList.toggle('hidden');
        });
    }
</script>
</body>
</html>