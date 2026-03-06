<?php
/**
 * Frontend Master Layout
 * Variables expected: $pageTitle, $content
 */
use App\Core\Session;
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\asset;

$flashSuccess = Session::getFlash('success');
$flashError = Session::getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Hệ thống Bình chọn Online') ?> - UnionVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('css/frontend.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-blue-800 text-white shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="<?= baseUrl('') ?>" class="flex items-center gap-3">
                <span class="text-xl font-bold">UnionVote</span>
            </a>
            <p class="text-blue-200 text-sm hidden sm:block">Đoàn Khoa CNTT - ĐH Nha Trang</p>
        </div>
    </header>

    <main class="flex-1 max-w-5xl mx-auto w-full px-4 py-8">
        <?php if ($flashSuccess): ?>
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                <?= e($flashSuccess) ?>
            </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                <?= e($flashError) ?>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <footer class="bg-white border-t border-gray-200 py-4">
        <div class="max-w-5xl mx-auto px-4 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> Đoàn Khoa Công nghệ Thông tin - Trường Đại học Nha Trang
        </div>
    </footer>

    <script src="<?= asset('js/frontend.js') ?>"></script>
</body>
</html>
