<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;

class RoleMiddleware
{
    private array $roles;

    public function __construct(array $roles = [])
    {
        $this->roles = $roles;
    }

    public static function for(array $roles): string
    {
        return self::class;
    }

    public function handle(Request $request): void
    {
        if (!is_auth()) {
            redirect('/login');
        }

        if (!empty($this->roles) && !has_role($this->roles)) {
            http_response_code(403);
            flash('error', 'You do not have permission to access this page.');
            redirect('/dashboard');
        }
    }
}
