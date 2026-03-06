<?php
use function App\Core\e;
use function App\Core\baseUrl;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Trang không tồn tại</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-blue-800 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-6">Trang bạn tìm không tồn tại.</p>
        <a href="/" class="inline-block bg-blue-800 text-white px-6 py-3 rounded-lg hover:bg-blue-900 transition font-medium">
            Về trang chủ
        </a>
    </div>
</body>
</html>
