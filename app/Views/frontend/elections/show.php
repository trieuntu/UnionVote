<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;
use function App\Core\computeStatus;
use function App\Core\formatDate;

$pageTitle = e($election['title']);
$status = computeStatus($election);
ob_start();
?>

<div class="mb-4">
    <a href="<?= baseUrl('') ?>" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Trang chủ</a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2"><?= e($election['title']) ?></h1>
    <?php if (!empty($election['description'])): ?>
    <p class="text-gray-600 mb-4"><?= nl2br(e($election['description'])) ?></p>
    <?php endif; ?>
    <div class="flex flex-wrap gap-6 text-sm text-gray-500">
        <span>Thời gian: <?= e(formatDate($election['start_time'])) ?> — <?= e(formatDate($election['end_time'])) ?></span>
        <span>Chọn tối thiểu <?= (int)$election['min_votes'] ?> người, tối đa <?= (int)$election['max_votes'] ?> người</span>
    </div>

    <?php if ($status === 'active'): ?>
    <div class="mt-6">
        <button onclick="document.getElementById('voteModal').classList.remove('hidden')" class="bg-blue-800 text-white px-6 py-3 rounded-lg hover:bg-blue-900 transition font-medium">
            Bỏ phiếu cho cuộc bình chọn này
        </button>
    </div>
    <?php elseif ($status === 'completed'): ?>
    <div class="mt-4">
        <span class="inline-block px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-medium">Cuộc bình chọn đã kết thúc</span>
    </div>
    <?php else: ?>
    <div class="mt-4">
        <span class="inline-block px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg font-medium">Cuộc bình chọn chưa bắt đầu</span>
    </div>
    <?php endif; ?>
</div>

<!-- Candidate List -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Danh sách ứng cử viên</h2>
    </div>
    <div class="divide-y divide-gray-200">
        <?php foreach ($candidates as $i => $c): ?>
        <div class="px-6 py-4">
            <div class="flex items-start gap-4">
                <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center font-bold text-sm"><?= $i + 1 ?></span>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800"><?= e($c['full_name']) ?></h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Lớp: <?= e($c['class_name']) ?> | MSSV: <?= e($c['student_id']) ?>
                        <?php if ($c['gpa'] !== null): ?> | ĐTB: <?= number_format((float)$c['gpa'], 2) ?><?php endif; ?>
                        <?php if ($c['conduct_score'] !== null): ?> | ĐRL: <?= number_format((float)$c['conduct_score'], 1) ?><?php endif; ?>
                    </p>
                    <?php if (!empty($c['bio'])): ?>
                    <p class="text-sm text-gray-600 mt-1"><?= e($c['bio']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Results (if show_result) -->
<?php if ($election['show_result'] && !empty($results)): ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Kết quả bình chọn</h2>
        <a href="<?= baseUrl('results/' . $election['id']) ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Xem realtime &rarr;</a>
    </div>
    <div class="p-6">
        <?php
        $maxVotes = max(array_column($results, 'vote_count'));
        $maxVotes = max($maxVotes, 1);
        ?>
        <div class="space-y-3">
            <?php foreach ($results as $r): ?>
            <?php $pct = $totalVoted > 0 ? round(($r['vote_count'] / $totalVoted) * 100, 1) : 0; ?>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium"><?= e($r['full_name']) ?></span>
                    <span class="text-gray-600"><?= (int)$r['vote_count'] ?> phiếu (<?= $pct ?>%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-5">
                    <div class="bg-blue-800 rounded-full h-5 transition-all" style="width: <?= $maxVotes > 0 ? round(($r['vote_count'] / $maxVotes) * 100) : 0 ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-sm text-gray-500 mt-4">Tổng số phiếu đã bầu: <?= $totalVoted ?>/<?= $totalVoters ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Vote Modal -->
<?php if ($status === 'active'): ?>
<div id="voteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Xác thực email để bỏ phiếu</h3>
        <form action="<?= baseUrl('vote/request-token') ?>" method="POST">
            <?= CSRF::field() ?>
            <input type="hidden" name="election_id" value="<?= (int)$election['id'] ?>">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email sinh viên</label>
                <input type="email" id="email" name="email" required placeholder="mssv@ntu.edu.vn"
                       pattern=".*@ntu\.edu\.vn$"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Sử dụng email @ntu.edu.vn của bạn</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="token-submit-btn flex-1 bg-blue-800 text-white py-2 rounded-lg hover:bg-blue-900 transition font-medium">
                    Gửi mã xác thực
                </button>
                <button type="button" onclick="document.getElementById('voteModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-600">
                    Huỷ
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
