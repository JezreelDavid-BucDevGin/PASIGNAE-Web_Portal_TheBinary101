<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;

class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (!is_auth()) {
            flash('error', 'Please log in to continue.');
            redirect('/login');
        }
    }
}
