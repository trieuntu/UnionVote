<?php
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Bỏ phiếu thành công';
ob_start();
?>

<div class="max-w-md mx-auto text-center">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="text-green-500 text-6xl mb-4">&#10004;</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Bỏ phiếu thành công!</h1>
        <p class="text-gray-500 mb-6">Cảm ơn bạn đã tham gia bình chọn.<br>Phiếu bầu của bạn đã được ghi nhận.</p>

        <div class="flex flex-col gap-3">
            <a href="<?= baseUrl('') ?>" class="bg-blue-800 text-white py-3 rounded-lg hover:bg-blue-900 transition font-medium">
                Về trang chủ
            </a>
            <?php if ($election && $election['show_result']): ?>
            <a href="<?= baseUrl('results/' . $election['id']) ?>" class="border border-blue-800 text-blue-800 py-3 rounded-lg hover:bg-blue-50 transition font-medium">
                Xem kết quả
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
