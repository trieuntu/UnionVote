<?php
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\statusLabel;

$pageTitle = 'Dashboard';
ob_start();
?>

<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-500">Tổng cuộc bình chọn</p>
        <p class="text-3xl font-bold text-blue-800 mt-1"><?= (int)($totalElections ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-500">Đang diễn ra</p>
        <p class="text-3xl font-bold text-green-600 mt-1"><?= (int)($activeElections ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-500">Phiếu bầu hôm nay</p>
        <p class="text-3xl font-bold text-purple-600 mt-1"><?= (int)($totalVotedToday ?? 0) ?></p>
    </div>
</div>

<?php if (!empty($elections)): ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Cuộc bình chọn gần đây</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($elections as $el): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?= e($el['title']) ?></td>
                    <td class="px-6 py-4"><?= statusLabel(\App\Core\computeStatus($el)) ?></td>
                    <td class="px-6 py-4 text-gray-500"><?= e(\App\Core\formatDate($el['start_time'])) ?> - <?= e(\App\Core\formatDate($el['end_time'])) ?></td>
                    <td class="px-6 py-4 text-right">
                        <a href="<?= baseUrl('admin/elections/' . $el['id']) ?>" class="text-blue-600 hover:text-blue-800 font-medium">Xem</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
