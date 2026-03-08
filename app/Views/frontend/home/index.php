<?php
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\computeStatus;
use function App\Core\formatDate;

$pageTitle = 'Trang chủ';
ob_start();
?>

<div class="text-center mb-8">
    <h1 class="text-3xl font-bold text-blue-800">Hệ thống Bình chọn Online</h1>
    <p class="text-gray-500 mt-2">Đoàn Khoa Công nghệ Thông tin - Trường Đại học Nha Trang</p>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
    <p class="text-blue-800 text-sm text-center font-medium">
        Hệ thống bỏ phiếu ẩn danh. Phiếu bầu không liên kết với thông tin cá nhân của người bỏ phiếu (token_hash không thể truy ngược tới email). Hệ thống ưu tiên theo thời gian bỏ phiếu sớm nếu cùng thứ hạng.
    </p>
</div>

<?php if (empty($elections)): ?>
<div class="text-center py-16">
    <p class="text-gray-400 text-lg">Hiện tại chưa có cuộc bình chọn nào.</p>
</div>
<?php else: ?>

<h2 class="text-xl font-semibold text-gray-800 mb-4">Cuộc bình chọn đang diễn ra</h2>

<div class="space-y-4">
    <?php foreach ($elections as $el): ?>
    <?php $status = computeStatus($el); ?>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800"><?= e($el['title']) ?></h3>
                <?php if (!empty($el['description'])): ?>
                <p class="text-gray-500 text-sm mt-1"><?= e(mb_strimwidth($el['description'], 0, 150, '...')) ?></p>
                <?php endif; ?>
                <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                    <span>Thời gian: <?= e(formatDate($el['start_time'])) ?> - <?= e(formatDate($el['end_time'])) ?></span>
                    <span>Chọn tối thiểu <?= (int)$el['min_votes'] ?>, tối đa <?= (int)$el['max_votes'] ?></span>
                </div>
                <?php if ($status === 'active'): ?>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Đang diễn ra</span>
                <?php elseif ($status === 'completed'): ?>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Đã kết thúc</span>
                <?php else: ?>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Sắp diễn ra</span>
                <?php endif; ?>
            </div>
            <div class="flex-shrink-0">
                <a href="<?= baseUrl('election/' . $el['id']) ?>" class="inline-block bg-blue-800 text-white px-5 py-2 rounded-lg hover:bg-blue-900 transition font-medium text-sm">
                    Xem chi tiết
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
