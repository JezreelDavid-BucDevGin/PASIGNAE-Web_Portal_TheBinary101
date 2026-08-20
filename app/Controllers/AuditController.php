<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AuditRepository;

class AuditController extends Controller
{
    public function index(): void
    {
        view('audit.index', [
            'layout' => 'app',
            'title' => 'Audit Logs',
            'logs' => (new AuditRepository())->getRecent(100),
        ]);
    }
}
