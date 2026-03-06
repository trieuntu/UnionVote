<?php
use App\Core\Session;
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\asset;

$flashError = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - UnionVote Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-blue-800">UnionVote Admin</h1>
            <p class="text-gray-500 mt-1">Đoàn Khoa CNTT - ĐH Nha Trang</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Đăng nhập</h2>

            <?php if ($flashError): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <?= e($flashError) ?>
                </div>
            <?php endif; ?>

            <form action="<?= baseUrl('admin/login') ?>" method="POST">
                <?= CSRF::field() ?>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" required autofocus
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-800 text-white py-2 px-4 rounded-lg hover:bg-blue-900 transition font-medium">
                    Đăng nhập
                </button>
            </form>
        </div>
        <p class="text-center text-sm text-gray-400 mt-6">&copy; <?= date('Y') ?> UnionVote</p>
    </div>
</body>
</html>
