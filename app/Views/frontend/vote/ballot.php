<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Phiếu bầu';
ob_start();
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">PHIẾU BẦU</h1>
        <p class="text-gray-500 mb-2"><?= e($election['title']) ?></p>
        <p class="text-sm text-blue-800 font-medium mb-6">
            Chọn tối thiểu <?= (int)$election['min_votes'] ?>, tối đa <?= (int)$election['max_votes'] ?> ứng cử viên.
            <span id="selectedCount" class="ml-2 text-gray-500">Đã chọn: 0/<?= (int)$election['max_votes'] ?></span>
        </p>

        <form id="ballotForm" action="<?= baseUrl('vote/submit') ?>" method="POST">
            <?= CSRF::field() ?>
            <input type="hidden" name="election_id" value="<?= (int)$election['id'] ?>">
            <input type="hidden" name="action" value="review">

            <div class="space-y-3 mb-6">
                <?php foreach ($candidates as $i => $c): ?>
                <label class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition candidate-item">
                    <input type="checkbox" name="candidates[]" value="<?= (int)$c['id'] ?>" class="candidate-checkbox mt-1 w-5 h-5 text-blue-800 rounded border-gray-300 focus:ring-blue-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-blue-800"><?= $i + 1 ?>.</span>
                            <span class="font-semibold text-gray-800"><?= e($c['full_name']) ?></span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Lớp: <?= e($c['class_name']) ?> | MSSV: <?= e($c['student_id']) ?>
                            <?php if ($c['gpa'] !== null): ?> | ĐTB: <?= number_format((float)$c['gpa'], 2) ?><?php endif; ?>
                            <?php if ($c['conduct_score'] !== null): ?> | ĐRL: <?= number_format((float)$c['conduct_score'], 1) ?><?php endif; ?>
                        </p>
                        <?php if (!empty($c['bio'])): ?>
                        <p class="text-sm text-gray-600 mt-1"><?= e($c['bio']) ?></p>
                        <?php endif; ?>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="flex gap-3">
                <button type="submit" id="reviewBtn" disabled class="flex-1 bg-blue-800 text-white py-3 rounded-lg hover:bg-blue-900 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    Xem lại trước khi gửi
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= \App\Core\asset('js/voting.js') ?>"></script>
<script>
    initBallot(<?= (int)$election['min_votes'] ?>, <?= (int)$election['max_votes'] ?>);
</script>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
