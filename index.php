<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Request;
use App\Core\Router;

$router = new Router();
$request = new Request();

require __DIR__ . '/routes/web.php';

$router->dispatch($request);
