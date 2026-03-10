<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Xác thực mã';
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Nhập mã xác thực</h1>
        <?php if (!empty($maskedEmail)): ?>
        <p class="text-gray-500 mb-6">Mã đã gửi đến: <strong><?= e($maskedEmail) ?></strong></p>
        <?php endif; ?>

        <form action="<?= baseUrl('vote/verify-token') ?>" method="POST">
            <?= CSRF::field() ?>
            <input type="hidden" name="election_id" value="<?= (int)$election['id'] ?>">
            <div class="mb-6">
                <input type="text" name="token" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus
                       placeholder="______"
                       class="w-full text-center text-3xl tracking-[0.5em] font-mono px-4 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <p class="text-xs text-gray-400 mt-2">Mã có hiệu lực trong 15 phút</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-800 text-white py-3 rounded-lg hover:bg-blue-900 transition font-medium">
                    Xác nhận
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-gray-100">
            <form action="<?= baseUrl('vote/request-token') ?>" method="POST" class="inline">
                <?= CSRF::field() ?>
                <input type="hidden" name="election_id" value="<?= (int)$election['id'] ?>">
                <input type="hidden" name="email" value="<?= e(\App\Core\Session::get('vote_email', '')) ?>">
                <button type="submit" class="token-submit-btn text-blue-600 hover:text-blue-800 text-sm font-medium">Gửi lại mã</button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/master.php';
?>
