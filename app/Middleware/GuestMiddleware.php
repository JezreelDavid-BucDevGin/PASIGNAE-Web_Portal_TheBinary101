<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;

class GuestMiddleware
{
    public function handle(Request $request): void
    {
        if (is_auth()) {
            redirect('/dashboard');
        }
    }
}
