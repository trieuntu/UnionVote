<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\LoginLog;

class LogController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 30;
        $offset = ($page - 1) * $perPage;

        $logModel = new LoginLog();
        $total = $logModel->countAll();
        $logs = $logModel->getRecent($perPage, $offset);
        $totalPages = max(1, (int)ceil($total / $perPage));

        $this->view('admin.logs.index', [
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }
}
