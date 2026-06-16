<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $ringkasan     = $this->dashboardService->getRingkasan();
        $grafik7Hari   = $this->dashboardService->getGrafik7Hari();
        $grafikBulanan = $this->dashboardService->getGrafikBulanan();

        return view('owner.dashboard.index', compact(
            'ringkasan', 'grafik7Hari', 'grafikBulanan'
        ));
    }
}