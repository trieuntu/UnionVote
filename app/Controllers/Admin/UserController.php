<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;
use App\Models\User;

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): void
    {
        $users = $this->userModel->findAll('id ASC');
        $this->view('admin.users.index', [
            'users' => $users,
            'user' => Auth::user(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.users.create', ['user' => Auth::user()]);
    }

    public function store(): void
    {
        $validator = new Validator();
        $valid = $validator->validate($_POST, [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'full_name' => 'required|max:100',
            'password' => 'required|min:6',
        ]);

        if (!$valid) {
            $this->setFlash('error', 'Dữ liệu không hợp lệ.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/users/create');
            return;
        }

        if ($this->userModel->findByUsername(trim($_POST['username']))) {
            $this->setFlash('error', 'Tên đăng nhập đã tồn tại.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/users/create');
            return;
        }

        $this->userModel->create([
            'username' => trim($_POST['username']),
            'email' => trim($_POST['email']),
            'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'full_name' => trim($_POST['full_name']),
            'role' => in_array($_POST['role'] ?? '', ['admin', 'editor']) ? $_POST['role'] : 'editor',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        $this->setFlash('success', 'Tạo tài khoản thành công!');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/users');
    }

    public function edit(string $id): void
    {
        $editUser = $this->userModel->find((int)$id);
        if (!$editUser) {
            $this->setFlash('error', 'Không tìm thấy người dùng.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/users');
            return;
        }

        $this->view('admin.users.edit', [
            'editUser' => $editUser,
            'user' => Auth::user(),
        ]);
    }

    public function update(string $id): void
    {
        $editUser = $this->userModel->find((int)$id);
        if (!$editUser) {
            $this->setFlash('error', 'Không tìm thấy người dùng.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/users');
            return;
        }

        $data = [
            'email' => trim($_POST['email'] ?? $editUser['email']),
            'full_name' => trim($_POST['full_name'] ?? $editUser['full_name']),
            'role' => in_array($_POST['role'] ?? '', ['admin', 'editor']) ? $_POST['role'] : $editUser['role'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_POST['password'])) {
            $data['password_hash'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        $this->userModel->update((int)$id, $data);
        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/users');
    }

    public function destroy(string $id): void
    {
        if ((int)$id === Auth::userId()) {
            $this->setFlash('error', 'Không thể xoá tài khoản đang sử dụng.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/users');
            return;
        }
        $this->userModel->delete((int)$id);
        $this->setFlash('success', 'Đã xoá tài khoản.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/users');
    }
}
