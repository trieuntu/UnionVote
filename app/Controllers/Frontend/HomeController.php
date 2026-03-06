<?php
namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Models\Election;

class HomeController extends Controller
{
    public function index(): void
    {
        $electionModel = new Election();
        $elections = $electionModel->getVisible();

        $this->view('frontend.home.index', [
            'elections' => $elections,
        ]);
    }
}
