<?php
namespace App\Core;

use App\Config\App;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function baseUrl(string $path = ''): string
{
    return App::baseUrl() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return App::baseUrl() . '/assets/' . ltrim($path, '/');
}

function old(string $key, string $default = ''): string
{
    return htmlspecialchars($_POST[$key] ?? Session::get("old.{$key}", $default), ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date, string $format = 'd/m/Y H:i'): string
{
    return (new \DateTime($date))->format($format);
}

function statusLabel(string $status): string
{
    return match($status) {
        'draft' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Nháp</span>',
        'active' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Đang diễn ra</span>',
        'completed' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Đã kết thúc</span>',
        'cancelled' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Đã huỷ</span>',
        default => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">' . e($status) . '</span>',
    };
}

function computeStatus(array $election): string
{
    if ($election['status'] === 'cancelled') return 'cancelled';
    $now = new \DateTime();
    $start = new \DateTime($election['start_time']);
    $end = new \DateTime($election['end_time']);
    if ($now < $start) return 'draft';
    if ($now >= $start && $now <= $end) return 'active';
    return 'completed';
}

function maskEmail(string $email): string
{
    $parts = explode('@', $email);
    $name = $parts[0];
    $domain = $parts[1] ?? '';
    $masked = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 3));
    return $masked . '@' . $domain;
}
