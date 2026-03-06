<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\statusLabel;
use function App\Core\computeStatus;
use function App\Core\formatDate;

$pageTitle = 'Cuộc bình chọn';
ob_start();
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Danh sách cuộc bình chọn</h1>
    <a href="<?= baseUrl('admin/elections/create') ?>" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition text-sm font-medium">
        + Tạo mới
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hiển thị</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($elections)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Chưa có cuộc bình chọn nào.</td></tr>
                <?php else: ?>
                <?php foreach ($elections as $el): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500"><?= (int)$el['id'] ?></td>
                    <td class="px-4 py-3 font-medium"><?= e($el['title']) ?></td>
                    <td class="px-4 py-3"><?= statusLabel(computeStatus($el)) ?></td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        <?= e(formatDate($el['start_time'])) ?><br>
                        <?= e(formatDate($el['end_time'])) ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="<?= baseUrl('admin/elections/' . $el['id'] . '/toggle-visibility') ?>" method="POST" class="inline">
                            <?= CSRF::field() ?>
                            <input type="hidden" name="_method" value="PATCH">
                            <button type="submit" class="text-xs font-medium px-2 py-1 rounded <?= $el['is_visible'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= $el['is_visible'] ? 'Hiện' : 'Ẩn' ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="<?= baseUrl('admin/elections/' . $el['id'] . '/toggle-result') ?>" method="POST" class="inline">
                            <?= CSRF::field() ?>
                            <input type="hidden" name="_method" value="PATCH">
                            <button type="submit" class="text-xs font-medium px-2 py-1 rounded <?= $el['show_result'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= $el['show_result'] ? 'Hiện' : 'Ẩn' ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="<?= baseUrl('admin/elections/' . $el['id']) ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Xem</a>
                        <a href="<?= baseUrl('admin/elections/' . $el['id'] . '/edit') ?>" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Sửa</a>
                        <form action="<?= baseUrl('admin/elections/' . $el['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Xác nhận xóa?')">
                            <?= CSRF::field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
