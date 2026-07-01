<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class StokDashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $stok = $this->dashboardService->getStokDashboard();

        return view('owner.dashboard.stok', compact('stok'));
    }
}
