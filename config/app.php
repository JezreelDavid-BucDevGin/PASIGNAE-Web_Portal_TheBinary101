<?php

$appUrl = 'http://localhost/Pasignae';

if (getenv('DDEV_PRIMARY_URL')) {
    $appUrl = rtrim((string) getenv('DDEV_PRIMARY_URL'), '/');
} elseif (!empty($_SERVER['HTTP_HOST'])) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $basePath = ($scriptDir === '/' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
    $appUrl = ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $basePath;
}

return [
    'name' => 'PASIGNAE',
    'full_name' => 'Diocese of Pasig Church Web Portal',
    'url' => $appUrl,
    'timezone' => 'Asia/Manila',
    'debug' => true,

    'session' => [
        'name' => 'pasignae_session',
        'lifetime' => 7200,
    ],

    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'pasignae101@gmail.com',
        'password' => 'nzmi ovua wppf mqop',
        'from_email' => 'pasignae101@gmail.com',
        'from_name' => 'PASIGNAE - Diocese of Pasig',
        'encryption' => 'tls',
    ],

    // 'google app password' => 'nzmi ovua wppf mqop'

    'otp' => [
        'length' => 6,
        'expiry_minutes' => 15,
    ],

    'scheduling' => [
        'baptism' => [
            'window_days' => 0,
            'window_label' => null,
            'ceremony_label' => 'Baptism',
            'require_full_future_window' => false,
        ],
        'matrimony' => [
            'window_days' => 7,
            'window_label' => '7-Day Marriage Preparation',
            'ceremony_label' => 'Marriage Ceremony',
            'require_full_future_window' => true,
        ],
        'funeral' => [
            'window_days' => 7,
            'window_label' => '7-Day Wake Period',
            'ceremony_label' => 'Funeral Service',
            'require_full_future_window' => false,
        ],
    ],

    'upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png'],
        'path' => __DIR__ . '/../storage/uploads',
    ],

    'roles' => [
        'super_admin' => 1,
        'diocese_admin' => 2,
        'parish_admin' => 3,
        'parish_staff' => 4,
        'parish_priest' => 5,
        'chancery' => 6,
        'parishioner' => 7,
    ],
];
