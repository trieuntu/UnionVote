<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\CSRF;
use App\Models\User;
use App\Models\LoginLog;
use function App\Core\baseUrl;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect(baseUrl('admin'));
            return;
        }
        $this->view('admin.auth.login');
    }

    public function login(): void
    {
        if (!CSRF::validate()) {
            $this->setFlash('error', 'Phiên làm việc hết hạn. Vui lòng thử lại.');
            $this->redirect(baseUrl('admin/login'));
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin.');
            $this->redirect(baseUrl('admin/login'));
            return;
        }

        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            (new LoginLog())->log($username, 'failed', $user['id'] ?? null, 'Sai tên đăng nhập hoặc mật khẩu');
            $this->setFlash('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
            $this->redirect(baseUrl('admin/login'));
            return;
        }

        if (!$user['is_active']) {
            (new LoginLog())->log($username, 'failed', $user['id'], 'Tài khoản bị vô hiệu hoá');
            $this->setFlash('error', 'Tài khoản đã bị vô hiệu hoá.');
            $this->redirect(baseUrl('admin/login'));
            return;
        }

        (new LoginLog())->log($username, 'success', $user['id']);
        Auth::login($user);
        $userModel->updateLastLogin($user['id']);

        $this->redirect(baseUrl('admin'));
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect(baseUrl('admin/login'));
    }
}
