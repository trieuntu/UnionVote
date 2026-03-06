<?php
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Kết quả - ' . e($election['title']);
$totalVoters = (int)($election['voter_count'] ?? 0);
$totalVoted = (int)($election['voted_count'] ?? 0);
ob_start();
?>

<div class="mb-6">
    <a href="<?= baseUrl('admin/elections/' . $election['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; <?= e($election['title']) ?></a>
    <div class="flex items-center justify-between mt-2">
        <h1 class="text-2xl font-bold text-gray-800">Kết quả bình chọn</h1>
        <a href="<?= baseUrl('admin/elections/' . $election['id'] . '/results/export') ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
            Xuất Excel
        </a>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Tổng cử tri</p>
        <p class="text-3xl font-bold text-blue-800"><?= $totalVoters ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Đã bỏ phiếu</p>
        <p class="text-3xl font-bold text-green-600"><?= $totalVoted ?> <span class="text-lg text-gray-400">/ <?= $totalVoters ?></span></p>
    </div>
</div>

<!-- Results Chart -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Biểu đồ kết quả</h2>
    <?php if (!empty($candidates)): ?>
    <?php
    $maxVotes = max(array_column($candidates, 'vote_count'));
    $maxVotes = max($maxVotes, 1);
    ?>
    <div class="space-y-3">
        <?php foreach ($candidates as $c): ?>
        <?php
        $pct = $totalVoted > 0 ? round(($c['vote_count'] / $totalVoted) * 100, 1) : 0;
        $barWidth = $maxVotes > 0 ? round(($c['vote_count'] / $maxVotes) * 100) : 0;
        ?>
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span class="font-medium"><?= e($c['full_name']) ?> <span class="text-gray-400">(<?= e($c['class_name']) ?>)</span></span>
                <span class="text-gray-600"><?= (int)$c['vote_count'] ?> phiếu (<?= $pct ?>%)</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-6">
                <div class="bg-blue-800 rounded-full h-6 transition-all" style="width: <?= $barWidth ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-gray-400 text-center py-8">Chưa có dữ liệu.</p>
    <?php endif; ?>
</div>

<!-- Detailed Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Chi tiết kết quả</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">STT</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ và tên</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lớp</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">MSSV</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số phiếu</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tỉ lệ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($candidates as $i => $c): ?>
                <?php $pct = $totalVoted > 0 ? round(($c['vote_count'] / $totalVoted) * 100, 1) : 0; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500"><?= $i + 1 ?></td>
                    <td class="px-4 py-3 font-medium"><?= e($c['full_name']) ?></td>
                    <td class="px-4 py-3"><?= e($c['class_name']) ?></td>
                    <td class="px-4 py-3"><?= e($c['student_id']) ?></td>
                    <td class="px-4 py-3 text-right font-bold"><?= (int)$c['vote_count'] ?></td>
                    <td class="px-4 py-3 text-right"><?= $pct ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
