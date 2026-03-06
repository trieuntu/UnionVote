<?php
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\statusLabel;
use function App\Core\computeStatus;
use function App\Core\formatDate;

$pageTitle = e($election['title']);
$computed = computeStatus($election);
ob_start();
?>

<div class="mb-6">
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= baseUrl('admin/elections') ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Danh sách</a>
    </div>
    <h1 class="text-2xl font-bold text-gray-800"><?= e($election['title']) ?></h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500 mb-1">Trạng thái</p>
        <div><?= statusLabel($computed) ?></div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500 mb-1">Thời gian</p>
        <p class="text-sm"><?= e(formatDate($election['start_time'])) ?> — <?= e(formatDate($election['end_time'])) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500 mb-1">Chọn tối thiểu / tối đa</p>
        <p class="text-sm font-medium"><?= (int)$election['min_votes'] ?> — <?= (int)$election['max_votes'] ?></p>
    </div>
</div>

<?php if (!empty($election['description'])): ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
    <p class="text-sm text-gray-500 mb-1">Mô tả</p>
    <p class="text-sm text-gray-700"><?= nl2br(e($election['description'])) ?></p>
</div>
<?php endif; ?>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 text-center">
        <p class="text-3xl font-bold text-blue-800"><?= (int)($election['candidate_count'] ?? 0) ?></p>
        <p class="text-sm text-gray-500 mt-1">Ứng cử viên</p>
        <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/candidates') ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium mt-2 inline-block">Quản lý &rarr;</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 text-center">
        <p class="text-3xl font-bold text-green-600"><?= (int)($election['voter_count'] ?? 0) ?></p>
        <p class="text-sm text-gray-500 mt-1">Cử tri</p>
        <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/voters') ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium mt-2 inline-block">Quản lý &rarr;</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 text-center">
        <p class="text-3xl font-bold text-purple-600"><?= (int)($election['voted_count'] ?? 0) ?></p>
        <p class="text-sm text-gray-500 mt-1">Đã bỏ phiếu</p>
        <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/results') ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium mt-2 inline-block">Kết quả &rarr;</a>
    </div>
</div>

<!-- Actions -->
<div class="flex flex-wrap gap-3">
    <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/edit') ?>" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm font-medium">Chỉnh sửa</a>
    <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/candidates') ?>" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition text-sm font-medium">Ứng cử viên</a>
    <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/voters') ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">Cử tri</a>
    <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/results') ?>" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm font-medium">Kết quả</a>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
