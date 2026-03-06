<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$isEdit = !empty($candidate);
$pageTitle = ($isEdit ? 'Sửa' : 'Thêm') . ' ứng cử viên - ' . e($election['title']);
ob_start();
?>

<div class="mb-6">
    <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/candidates') ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Danh sách ứng cử viên</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $isEdit ? 'Sửa ứng cử viên' : 'Thêm ứng cử viên' ?></h1>
    <p class="text-gray-500 text-sm mt-1"><?= e($election['title']) ?></p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
    <form action="<?= $isEdit ? baseUrl('admin/candidates/' . $candidate['id']) : baseUrl('admin/elections/' . $election['id'] . '/candidates') ?>" method="POST">
        <?= CSRF::field() ?>
        <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="space-y-4">
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" id="full_name" required maxlength="100"
                       value="<?= e($candidate['full_name'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="class_name" class="block text-sm font-medium text-gray-700 mb-1">Lớp <span class="text-red-500">*</span></label>
                    <input type="text" name="class_name" id="class_name" required maxlength="50"
                           value="<?= e($candidate['class_name'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">MSSV <span class="text-red-500">*</span></label>
                    <input type="text" name="student_id" id="student_id" required maxlength="20"
                           value="<?= e($candidate['student_id'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="gpa" class="block text-sm font-medium text-gray-700 mb-1">Điểm TB tích luỹ</label>
                    <input type="number" name="gpa" id="gpa" step="0.01" min="0" max="10"
                           value="<?= e($candidate['gpa'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="conduct_score" class="block text-sm font-medium text-gray-700 mb-1">Điểm rèn luyện tích luỹ</label>
                    <input type="number" name="conduct_score" id="conduct_score" step="0.1" min="0" max="100"
                           value="<?= e($candidate['conduct_score'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt thông tin cá nhân</label>
                <textarea name="bio" id="bio" rows="3" maxlength="1000"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= e($candidate['bio'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" class="bg-blue-800 text-white px-5 py-2 rounded-lg hover:bg-blue-900 transition text-sm font-medium">
                <?= $isEdit ? 'Cập nhật' : 'Thêm ứng cử viên' ?>
            </button>
            <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/candidates') ?>" class="text-gray-500 hover:text-gray-700 text-sm">Huỷ</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
