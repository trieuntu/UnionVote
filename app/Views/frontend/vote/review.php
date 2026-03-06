<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Xác nhận phiếu bầu';
$candidateList = is_array($candidates) ? array_values($candidates) : [];
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">XÁC NHẬN PHIẾU BẦU</h1>
        <p class="text-gray-500 mb-6"><?= e($election['title']) ?></p>

        <p class="text-sm text-gray-600 mb-4">Bạn đã chọn <strong><?= count($candidateList) ?></strong> ứng cử viên:</p>

        <div class="space-y-3 mb-6">
            <?php foreach ($candidateList as $i => $c): ?>
            <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <span class="text-green-600 font-bold">&#10003;</span>
                <div>
                    <span class="font-semibold"><?= e($c['full_name']) ?></span>
                    <span class="text-sm text-gray-500 ml-2"><?= e($c['class_name']) ?> - <?= e($c['student_id']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800 text-sm font-medium">&#9888; Sau khi gửi, bạn không thể thay đổi phiếu bầu.</p>
        </div>

        <div class="flex gap-3">
            <a href="<?= baseUrl('vote/' . $election['id'] . '/ballot') ?>" class="flex-1 text-center border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition font-medium">
                Quay lại chỉnh sửa
            </a>
            <form action="<?= baseUrl('vote/submit') ?>" method="POST" class="flex-1">
                <?= CSRF::field() ?>
                <input type="hidden" name="election_id" value="<?= (int)$election['id'] ?>">
                <input type="hidden" name="action" value="submit">
                <?php foreach ($candidateList as $c): ?>
                <input type="hidden" name="candidates[]" value="<?= (int)$c['id'] ?>">
                <?php endforeach; ?>
                <button type="submit" class="w-full bg-blue-800 text-white py-3 rounded-lg hover:bg-blue-900 transition font-medium">
                    Xác nhận gửi phiếu
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
