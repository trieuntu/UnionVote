<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Cấu hình SMTP';
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Cấu hình SMTP Email</h1>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form action="<?= baseUrl('admin/settings/mail') ?>" method="POST">
            <?= CSRF::field() ?>

            <div class="mb-4">
                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Tên hệ thống</label>
                <input type="text" id="site_name" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="token_expiry_minutes" class="block text-sm font-medium text-gray-700 mb-1">Thời hạn token (phút)</label>
                <input type="number" id="token_expiry_minutes" name="token_expiry_minutes" min="1" value="<?= e($settings['token_expiry_minutes'] ?? '15') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <hr class="my-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Cấu hình SMTP</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                    <input type="number" id="smtp_port" name="smtp_port" value="<?= e($settings['smtp_port'] ?? '587') ?>" placeholder="587"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                    <input type="text" id="smtp_username" name="smtp_username" value="<?= e($settings['smtp_username'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-1">SMTP Password <span class="text-gray-400 text-xs">(để trống nếu không đổi)</span></label>
                    <input type="password" id="smtp_password" name="smtp_password" placeholder="••••••••"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="mb-4">
                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-1">Mã hóa</label>
                <select id="smtp_encryption" name="smtp_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="smtp_from_email" class="block text-sm font-medium text-gray-700 mb-1">From Email</label>
                    <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?= e($settings['smtp_from_email'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="smtp_from_name" class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                    <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?= e($settings['smtp_from_name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <button type="submit" class="bg-blue-800 text-white px-6 py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                Lưu cấu hình
            </button>
        </form>
    </div>

    <!-- Test Connection -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Kiểm tra kết nối</h3>
        <p class="text-sm text-gray-500 mb-4">Gửi email test để kiểm tra cấu hình SMTP hoạt động.</p>
        <form action="<?= baseUrl('admin/settings/mail/test') ?>" method="POST">
            <?= CSRF::field() ?>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                Gửi mail test
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
