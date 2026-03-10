<?php
/**
 * Admin Master Layout
 * Variables expected: $pageTitle, $content, $user
 */
use App\Core\Session;
use App\Core\Auth;
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\asset;

$flashSuccess = Session::getFlash('success');
$flashError = Session::getFlash('error');
$currentUser = $user ?? Auth::user();
$adminBase = baseUrl('admin');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - UnionVote Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="bg-gray-50 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white min-h-screen flex-shrink-0 hidden md:block">
        <div class="p-4 border-b border-blue-800">
            <a href="<?= $adminBase ?>" class="text-lg font-bold">UnionVote Admin</a>
        </div>
        <nav class="mt-4">
            <a href="<?= $adminBase ?>" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 transition">
                <span>Dashboard</span>
            </a>
            <div class="border-t border-blue-800 my-2"></div>
            <p class="px-4 py-1 text-xs text-blue-400 uppercase tracking-wider">Bình chọn</p>
            <a href="<?= $adminBase ?>/elections" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 transition">
                <span>Danh sách</span>
            </a>
            <a href="<?= $adminBase ?>/elections/create" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 transition">
                <span>Tạo mới</span>
            </a>
            <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
            <div class="border-t border-blue-800 my-2"></div>
            <p class="px-4 py-1 text-xs text-blue-400 uppercase tracking-wider">Quản lý</p>
            <a href="<?= $adminBase ?>/users" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 transition">
                <span>Người dùng</span>
            </a>
            <a href="<?= $adminBase ?>/settings/mail" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 transition">
                <span>Cấu hình SMTP</span>
            </a>
            <a href="<?= $adminBase ?>/logs" class="flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800 transition">
                <span>Nhật ký đăng nhập</span>
            </a>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-3">
                <button id="sidebarToggle" class="md:hidden text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-4 ml-auto">
                    <span class="text-sm text-gray-600">Xin chào, <strong><?= e($currentUser['full_name'] ?? '') ?></strong></span>
                    <form action="<?= $adminBase ?>/logout" method="POST" class="inline">
                        <?= CSRF::field() ?>
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            <?php if ($flashSuccess): ?>
                <div class="flash-message mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    <?= e($flashSuccess) ?>
                </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="flash-message mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <?= e($flashError) ?>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>

        <footer class="bg-white border-t border-gray-200 py-3 px-6 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> UnionVote - Đoàn Khoa CNTT, ĐH Nha Trang
        </footer>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
