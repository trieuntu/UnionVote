<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Setting;
use App\Services\MailService;

class SettingController extends Controller
{
    public function mail(): void
    {
        $setting = new Setting();
        $settings = $setting->getMultiple([
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
            'smtp_encryption', 'smtp_from_email', 'smtp_from_name',
            'site_name', 'token_expiry_minutes'
        ]);

        $this->view('admin.settings.mail', [
            'settings' => $settings,
            'user' => Auth::user(),
        ]);
    }

    public function updateMail(): void
    {
        $setting = new Setting();

        $setting->set('smtp_host', trim($_POST['smtp_host'] ?? ''));
        $setting->set('smtp_port', trim($_POST['smtp_port'] ?? '587'));
        $setting->set('smtp_username', trim($_POST['smtp_username'] ?? ''));
        $setting->set('smtp_encryption', in_array($_POST['smtp_encryption'] ?? '', ['tls', 'ssl']) ? $_POST['smtp_encryption'] : 'tls');
        $setting->set('smtp_from_email', trim($_POST['smtp_from_email'] ?? ''));
        $setting->set('smtp_from_name', trim($_POST['smtp_from_name'] ?? ''));
        $setting->set('site_name', trim($_POST['site_name'] ?? ''));
        $setting->set('token_expiry_minutes', trim($_POST['token_expiry_minutes'] ?? '15'));

        if (!empty($_POST['smtp_password'])) {
            $setting->set('smtp_password', MailService::encryptPassword($_POST['smtp_password']));
        }

        $this->setFlash('success', 'Cập nhật cấu hình thành công!');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/settings/mail');
    }

    public function testMail(): void
    {
        $mailService = new MailService();
        $result = $mailService->testConnection();

        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('error', $result['message']);
        }

        $this->redirect(\App\Config\App::baseUrl() . '/admin/settings/mail');
    }
}
