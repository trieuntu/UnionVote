<?php
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Nhật ký đăng nhập';
ob_start();
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Nhật ký đăng nhập</h1>
        <p class="text-sm text-gray-500 mt-1">Tổng cộng <?= number_format($total) ?> bản ghi</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Thời gian</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Username</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Họ tên</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Lý do thất bại</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">IP</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Trình duyệt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Chưa có nhật ký nào.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($logs as $i => $log): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500"><?= ($page - 1) * 30 + $i + 1 ?></td>
                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap"><?= e($log['created_at']) ?></td>
                    <td class="px-4 py-3 font-medium text-gray-800"><?= e($log['username']) ?></td>
                    <td class="px-4 py-3 text-gray-600"><?= e($log['full_name'] ?? '—') ?></td>
                    <td class="px-4 py-3">
                        <?php if ($log['status'] === 'success'): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Thành công</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Thất bại</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs"><?= e($log['failure_reason'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs"><?= e($log['ip_address']) ?></td>
                    <td class="px-4 py-3 text-gray-400 text-xs max-w-[200px] truncate" title="<?= e($log['user_agent'] ?? '') ?>"><?= e(mb_substr($log['user_agent'] ?? '', 0, 60)) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">Trang <?= $page ?> / <?= $totalPages ?></p>
        <div class="flex gap-1">
            <?php if ($page > 1): ?>
            <a href="<?= baseUrl('admin/logs?page=' . ($page - 1)) ?>" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">&laquo; Trước</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($p = $start; $p <= $end; $p++):
            ?>
            <a href="<?= baseUrl('admin/logs?page=' . $p) ?>"
               class="px-3 py-1 text-sm border rounded <?= $p === $page ? 'bg-blue-800 text-white border-blue-800' : 'border-gray-300 hover:bg-gray-50' ?>">
                <?= $p ?>
            </a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="<?= baseUrl('admin/logs?page=' . ($page + 1)) ?>" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">Sau &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
