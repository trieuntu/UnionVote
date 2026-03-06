<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\Setting;

class MailService
{
    private array $config;

    public function __construct()
    {
        $setting = new Setting();
        $this->config = [
            'host' => $setting->get('smtp_host', ''),
            'port' => (int)$setting->get('smtp_port', 587),
            'username' => $setting->get('smtp_username', ''),
            'password' => self::decryptPassword($setting->get('smtp_password', '')),
            'encryption' => $setting->get('smtp_encryption', 'tls'),
            'from_email' => $setting->get('smtp_from_email', ''),
            'from_name' => $setting->get('smtp_from_name', 'UnionVote'),
        ];
    }

    public function sendToken(string $toEmail, string $token, string $electionTitle, int $expiryMinutes): bool
    {
        $subject = '[UnionVote] Mã xác thực bỏ phiếu';
        $body = $this->buildTokenEmailBody($token, $electionTitle, $expiryMinutes);
        return $this->send($toEmail, $subject, $body);
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail send error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function testConnection(): array
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($this->config['from_email']);
            $mail->isHTML(true);
            $mail->Subject = '[UnionVote] Test kết nối SMTP';
            $mail->Body = '<p>Kết nối SMTP thành công! Hệ thống UnionVote đã sẵn sàng gửi email.</p>';

            $mail->send();
            return ['success' => true, 'message' => 'Gửi mail test thành công!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi: ' . $mail->ErrorInfo];
        }
    }

    private function buildTokenEmailBody(string $token, string $electionTitle, int $expiryMinutes): string
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;">
            <h2 style="color: #1E40AF; margin-bottom: 20px;">UnionVote - Mã xác thực bỏ phiếu</h2>
            <p>Bạn đang yêu cầu bỏ phiếu cho cuộc bình chọn:</p>
            <p style="font-weight: bold; color: #1E40AF;">' . htmlspecialchars($electionTitle, ENT_QUOTES, 'UTF-8') . '</p>
            <div style="background-color: #F3F4F6; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0 0 8px 0; color: #6B7280; font-size: 14px;">Mã xác thực của bạn:</p>
                <p style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1E40AF; margin: 0;">' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '</p>
            </div>
            <p style="color: #EF4444; font-size: 14px;">⏰ Mã có hiệu lực trong ' . $expiryMinutes . ' phút.</p>
            <p style="color: #6B7280; font-size: 13px;">Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
            <hr style="border: none; border-top: 1px solid #E5E7EB; margin: 20px 0;">
            <p style="color: #9CA3AF; font-size: 12px;">Hệ thống Bình chọn Online - Đoàn Khoa CNTT - ĐH Nha Trang</p>
        </div>';
    }

    public static function encryptPassword(string $password): string
    {
        if (empty($password)) return '';
        $key = $_ENV['APP_ENCRYPTION_KEY'] ?? '';
        $key = substr(hash('sha256', $key), 0, 32);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($password, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . '::' . $encrypted);
    }

    public static function decryptPassword(string $encrypted): string
    {
        if (empty($encrypted)) return '';
        $key = $_ENV['APP_ENCRYPTION_KEY'] ?? '';
        $key = substr(hash('sha256', $key), 0, 32);
        $data = base64_decode($encrypted);
        if ($data === false || !str_contains($data, '::')) return $encrypted;
        [$iv, $encryptedData] = explode('::', $data, 2);
        $decrypted = openssl_decrypt($encryptedData, 'AES-256-CBC', $key, 0, $iv);
        return $decrypted !== false ? $decrypted : '';
    }
}
