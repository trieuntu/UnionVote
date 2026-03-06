<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Sửa cuộc bình chọn';
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Chỉnh sửa cuộc bình chọn</h1>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="<?= baseUrl('admin/elections/' . $election['id']) ?>" method="POST">
            <?= CSRF::field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required
                       value="<?= e($election['title']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"><?= e($election['description'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian bắt đầu <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="start_time" name="start_time" required
                           value="<?= date('Y-m-d\TH:i', strtotime($election['start_time'])) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Thời gian kết thúc <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="end_time" name="end_time" required
                           value="<?= date('Y-m-d\TH:i', strtotime($election['end_time'])) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="min_votes" class="block text-sm font-medium text-gray-700 mb-1">Số tối thiểu phải chọn</label>
                    <input type="number" id="min_votes" name="min_votes" min="1" required
                           value="<?= (int)$election['min_votes'] ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label for="max_votes" class="block text-sm font-medium text-gray-700 mb-1">Số tối đa được chọn</label>
                    <input type="number" id="max_votes" name="max_votes" min="1" required
                           value="<?= (int)$election['max_votes'] ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="draft" <?= $election['status'] === 'draft' ? 'selected' : '' ?>>Nháp</option>
                    <option value="active" <?= $election['status'] === 'active' ? 'selected' : '' ?>>Đang diễn ra</option>
                    <option value="completed" <?= $election['status'] === 'completed' ? 'selected' : '' ?>>Đã kết thúc</option>
                    <option value="cancelled" <?= $election['status'] === 'cancelled' ? 'selected' : '' ?>>Đã huỷ</option>
                </select>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-800 text-white px-6 py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                    Cập nhật
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
