<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Ứng cử viên - ' . e($election['title']);
ob_start();
?>

<div class="mb-6">
    <a href="<?= baseUrl('admin/elections/' . $election['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; <?= e($election['title']) ?></a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Quản lý ứng cử viên</h1>
</div>

<!-- Import Form -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-3">Import danh sách</h2>
    <form action="<?= baseUrl('admin/elections/' . $election['id'] . '/candidates/import') ?>" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
        <?= CSRF::field() ?>
        <div>
            <label class="block text-sm text-gray-600 mb-1">File Excel/CSV</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                   class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
        <button type="submit" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition text-sm font-medium">Import</button>
        <a href="<?= baseUrl('admin/templates/candidates') ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Tải file mẫu</a>
    </form>
</div>

<!-- Candidates Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Danh sách (<?= count($candidates) ?>)</h2>
        <?php if (!empty($candidates)): ?>
        <form action="<?= baseUrl('admin/elections/' . $election['id'] . '/candidates') ?>" method="POST" onsubmit="return confirm('Xóa tất cả ứng cử viên?')">
            <?= CSRF::field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Xoá tất cả</button>
        </form>
        <?php endif; ?>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ và tên</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lớp</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">MSSV</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ĐTB</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">ĐRL</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thông tin</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($candidates)): ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Chưa có ứng cử viên. Hãy import từ file Excel/CSV.</td></tr>
                <?php else: ?>
                <?php foreach ($candidates as $i => $c): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500"><?= $i + 1 ?></td>
                    <td class="px-4 py-3 font-medium"><?= e($c['full_name']) ?></td>
                    <td class="px-4 py-3"><?= e($c['class_name']) ?></td>
                    <td class="px-4 py-3"><?= e($c['student_id']) ?></td>
                    <td class="px-4 py-3 text-right"><?= $c['gpa'] !== null ? number_format((float)$c['gpa'], 2) : '-' ?></td>
                    <td class="px-4 py-3 text-right"><?= $c['conduct_score'] !== null ? number_format((float)$c['conduct_score'], 1) : '-' ?></td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate"><?= e($c['bio'] ?? '') ?></td>
                    <td class="px-4 py-3 text-right">
                        <form action="<?= baseUrl('admin/candidates/' . $c['id']) ?>" method="POST" onsubmit="return confirm('Xóa ứng cử viên này?')" class="inline">
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
