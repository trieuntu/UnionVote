<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\old;

$pageTitle = 'Tạo người dùng';
ob_start();
?>

<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tạo người dùng mới</h1>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="<?= baseUrl('admin/users') ?>" method="POST">
            <?= CSRF::field() ?>

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập <span class="text-red-500">*</span></label>
                <input type="text" id="username" name="username" required value="<?= old('username') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                <input type="text" id="full_name" name="full_name" required value="<?= old('full_name') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" required value="<?= old('email') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                <input type="password" id="password" name="password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Quyền</label>
                <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">Kích hoạt</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-800 text-white px-6 py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                    Tạo tài khoản
                </button>
                <a href="<?= baseUrl('admin/users') ?>" class="text-gray-600 hover:text-gray-800">Huỷ</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
