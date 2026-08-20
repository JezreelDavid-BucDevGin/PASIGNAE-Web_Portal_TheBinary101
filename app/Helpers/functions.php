<?php

declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = require BASE_PATH . '/config/app.php';
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!is_array($value) || !array_key_exists($k, $value)) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

function base_url(string $path = ''): string
{
    $base = rtrim(config('url'), '/');
    $path = ltrim($path, '/');
    return $path ? "{$base}/{$path}" : $base;
}

function asset(string $path): string
{
    return base_url('public/' . ltrim($path, '/'));
}

function redirect(string $url): never
{
    if (!str_starts_with($url, 'http')) {
        $url = base_url(ltrim($url, '/'));
    }
    header("Location: {$url}");
    exit;
}

function view(string $template, array $data = []): void
{
    extract($data);
    $file = VIEW_PATH . '/' . str_replace('.', '/', $template) . '.php';

    if (!file_exists($file)) {
        throw new RuntimeException("View [{$template}] not found.");
    }

    ob_start();
    require $file;
    $content = ob_get_clean();

    if (!isset($layout)) {
        echo $content;
        return;
    }

    $viewContent = $content;
    require VIEW_PATH . '/layouts/' . $layout . '.php';
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $message;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_auth(): bool
{
    return auth() !== null;
}

function has_role(string|array $roles): bool
{
    $user = auth();
    if (!$user) {
        return false;
    }

    $roles = (array) $roles;
    return in_array($user['role_slug'], $roles, true);
}

function can_access(string $permission): bool
{
    $user = auth();
    if (!$user) {
        return false;
    }

    $permissions = [
        'super_admin' => ['*'],
        'diocese_admin' => ['dashboard.diocese', 'parishes', 'reports', 'users', 'sacraments', 'schedules', 'payments', 'audit'],
        'parish_admin' => ['dashboard.parish', 'parishes.view', 'reports.parish', 'users.parish', 'sacraments', 'schedules', 'payments', 'audit'],
        'parish_staff' => ['dashboard.parish', 'sacraments', 'schedules', 'payments'],
        'parish_priest' => ['dashboard.parish', 'sacraments', 'schedules'],
        'chancery' => ['dashboard.diocese', 'reports', 'sacraments.view', 'audit'],
        'parishioner' => ['dashboard.user', 'sacraments', 'payments'],
    ];

    $rolePerms = $permissions[$user['role_slug']] ?? [];

    if (in_array('*', $rolePerms, true)) {
        return true;
    }

    foreach ($rolePerms as $perm) {
        if ($perm === $permission || str_ends_with($perm, '.*') && str_starts_with($permission, rtrim($perm, '.*'))) {
            return true;
        }
        if (str_ends_with($perm, '.view') && str_starts_with($permission, str_replace('.view', '', $perm))) {
            return true;
        }
    }

    return in_array($permission, $rolePerms, true);
}

function format_date(?string $date, string $format = 'M d, Y'): string
{
    if (!$date) {
        return '—';
    }
    return date($format, strtotime($date));
}

function format_currency(float $amount): string
{
    return '₱' . number_format($amount, 2);
}
