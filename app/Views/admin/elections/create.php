<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\old;

$pageTitle = 'Tạo cuộc bình chọn';
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tạo cuộc bình chọn mới</h1>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="<?= baseUrl('admin/elections') ?>" method="POST">
            <?= CSRF::field() ?>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required value="<?= old('title') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"><?= old('description') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian bắt đầu <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="start_time" name="start_time" required value="<?= old('start_time') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian kết thúc <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="end_time" name="end_time" required value="<?= old('end_time') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="min_votes" class="block text-sm font-medium text-gray-700 mb-1">Số tối thiểu phải chọn <span class="text-red-500">*</span></label>
                    <input type="number" id="min_votes" name="min_votes" min="1" required value="<?= old('min_votes', '1') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="max_votes" class="block text-sm font-medium text-gray-700 mb-1">Số tối đa được chọn <span class="text-red-500">*</span></label>
                    <input type="number" id="max_votes" name="max_votes" min="1" required value="<?= old('max_votes', '1') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-800 text-white px-6 py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                    Tạo cuộc bình chọn
                </button>
                <a href="<?= baseUrl('admin/elections') ?>" class="text-gray-600 hover:text-gray-800">Huỷ</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
