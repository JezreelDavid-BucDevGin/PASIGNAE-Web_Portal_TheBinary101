<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', BASE_PATH . '/views');
define('STORAGE_PATH', BASE_PATH . '/storage');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

require_once APP_PATH . '/Helpers/functions.php';

$config = require BASE_PATH . '/config/app.php';
$dbConfig = require BASE_PATH . '/config/database.php';

date_default_timezone_set($config['timezone']);

App\Core\Session::start($config['session']);
App\Core\Database::connect($dbConfig);
