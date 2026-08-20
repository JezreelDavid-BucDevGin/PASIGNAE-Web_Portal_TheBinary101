<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    private DashboardService $dashboard;

    public function __construct()
    {
        parent::__construct();
        $this->dashboard = new DashboardService();
    }

    public function index(): void
    {
        $user = auth();
        $role = $user['role_slug'];

        if (in_array($role, ['super_admin', 'diocese_admin', 'chancery'], true)) {
            $stats = $this->dashboard->getDioceseStats();
            view('dashboard.diocese', [
                'layout' => 'app',
                'title' => 'Diocese Dashboard',
                'stats' => $stats,
            ]);
            return;
        }

        if (in_array($role, ['parish_admin', 'parish_staff', 'parish_priest'], true)) {
            $stats = $this->dashboard->getParishStats((int) $user['parish_id']);
            view('dashboard.parish', [
                'layout' => 'app',
                'title' => 'Parish Dashboard',
                'stats' => $stats,
            ]);
            return;
        }

        $stats = $this->dashboard->getUserStats((int) $user['id']);
        view('dashboard.user', [
            'layout' => 'app',
            'title' => 'My Dashboard',
            'stats' => $stats,
        ]);
    }
}
