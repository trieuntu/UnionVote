<?php
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Kết quả - ' . e($election['title']);
ob_start();
?>

<div class="mb-4">
    <a href="<?= baseUrl('election/' . $election['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Kết quả bình chọn</h1>
    <p class="text-gray-500"><?= e($election['title']) ?></p>
    <div class="flex gap-6 mt-4">
        <div class="text-center">
            <p class="text-3xl font-bold text-blue-800" id="totalVoted"><?= $totalVoted ?></p>
            <p class="text-sm text-gray-500">Đã bỏ phiếu</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-gray-400"><?= $totalVoters ?></p>
            <p class="text-sm text-gray-500">Tổng cử tri</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="resultsContainer">
    <?php if (!empty($candidates)): ?>
    <?php
    $maxVotes = max(array_column($candidates, 'vote_count'));
    $maxVotes = max($maxVotes, 1);
    ?>
    <div class="space-y-4" id="resultBars">
        <?php foreach ($candidates as $c): ?>
        <?php $pct = $totalVoted > 0 ? round(($c['vote_count'] / $totalVoted) * 100, 1) : 0; ?>
        <div class="result-item" data-candidate-id="<?= (int)$c['id'] ?>">
            <div class="flex justify-between text-sm mb-1">
                <span class="font-medium"><?= e($c['full_name']) ?> <span class="text-gray-400">(<?= e($c['class_name']) ?>)</span></span>
                <span class="text-gray-600 vote-info"><?= (int)$c['vote_count'] ?> phiếu (<?= $pct ?>%)</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-6">
                <div class="bg-blue-800 rounded-full h-6 transition-all duration-500 vote-bar" style="width: <?= $maxVotes > 0 ? round(($c['vote_count'] / $maxVotes) * 100) : 0 ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-gray-400 text-center py-8">Chưa có dữ liệu.</p>
    <?php endif; ?>

    <p class="text-xs text-gray-400 mt-6 text-center">Tự động cập nhật mỗi 10 giây</p>
</div>

<script src="<?= \App\Core\asset('js/realtime.js') ?>"></script>
<script>
    startPolling(<?= (int)$election['id'] ?>);
</script>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
