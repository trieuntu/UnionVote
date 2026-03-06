<?php
use App\Core\CSRF;
use function App\Core\e;
use function App\Core\baseUrl;

$pageTitle = 'Người dùng';
ob_start();
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Quản lý người dùng</h1>
    <a href="<?= baseUrl('admin/users/create') ?>" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition text-sm font-medium">
        + Tạo mới
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ tên</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quyền</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500"><?= (int)$u['id'] ?></td>
                    <td class="px-4 py-3 font-medium"><?= e($u['username']) ?></td>
                    <td class="px-4 py-3"><?= e($u['full_name']) ?></td>
                    <td class="px-4 py-3"><?= e($u['email']) ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $u['role'] === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' ?>">
                            <?= e($u['role']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $u['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                            <?= $u['is_active'] ? 'Hoạt động' : 'Khóa' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="<?= baseUrl('admin/users/' . $u['id'] . '/edit') ?>" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Sửa</a>
                        <form action="<?= baseUrl('admin/users/' . $u['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Xác nhận xóa?')">
                            <?= CSRF::field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Xóa</button>
                        </form>
                    </td>
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
