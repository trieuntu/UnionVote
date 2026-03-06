<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$isEdit = !empty($voter);
$pageTitle = ($isEdit ? 'Sửa' : 'Thêm') . ' cử tri - ' . e($election['title']);
ob_start();
?>

<div class="mb-6">
    <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/voters') ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Danh sách cử tri</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $isEdit ? 'Sửa cử tri' : 'Thêm cử tri' ?></h1>
    <p class="text-gray-500 text-sm mt-1"><?= e($election['title']) ?></p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-lg">
    <form action="<?= $isEdit ? baseUrl('admin/voters/' . $voter['id']) : baseUrl('admin/elections/' . $election['id'] . '/voters') ?>" method="POST">
        <?= CSRF::field() ?>
        <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" required maxlength="100"
                   value="<?= e($voter['email'] ?? '') ?>"
                   placeholder="example@ntu.edu.vn"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <?php if ($isEdit && $voter['has_voted']): ?>
        <p class="text-yellow-600 text-xs mt-2">Cử tri này đã bỏ phiếu. Chỉ có thể sửa email.</p>
        <?php endif; ?>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="bg-blue-800 text-white px-5 py-2 rounded-lg hover:bg-blue-900 transition text-sm font-medium">
                <?= $isEdit ? 'Cập nhật' : 'Thêm cử tri' ?>
            </button>
            <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/voters') ?>" class="text-gray-500 hover:text-gray-700 text-sm">Huỷ</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
