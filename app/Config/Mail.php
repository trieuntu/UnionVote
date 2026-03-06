<?php
namespace App\Config;

class Mail
{
    public static function getConfig(): array
    {
        $setting = new \App\Models\Setting();
        return [
            'host' => $setting->get('smtp_host', ''),
            'port' => (int)$setting->get('smtp_port', 587),
            'username' => $setting->get('smtp_username', ''),
            'password' => $setting->get('smtp_password', ''),
            'encryption' => $setting->get('smtp_encryption', 'tls'),
            'from_email' => $setting->get('smtp_from_email', ''),
            'from_name' => $setting->get('smtp_from_name', 'UnionVote'),
        ];
    }
}
