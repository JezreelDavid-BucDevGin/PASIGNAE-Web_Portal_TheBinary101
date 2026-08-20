<?php

declare(strict_types=1);

use App\Controllers\AuditController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ParishController;
use App\Controllers\PaymentController;
use App\Controllers\SacramentController;
use App\Controllers\ScheduleController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$auth = [AuthMiddleware::class];
$guest = [GuestMiddleware::class];

// Public
$router->get('/', [HomeController::class, 'index']);

// Auth (guest only)
$router->get('/login', [AuthController::class, 'showLogin'], $guest);
$router->post('/login', [AuthController::class, 'login'], $guest);
$router->get('/register', [AuthController::class, 'showRegister'], $guest);
$router->post('/register', [AuthController::class, 'register'], $guest);
$router->get('/verify-otp', [AuthController::class, 'showVerifyOtp'], $guest);
$router->post('/verify-otp', [AuthController::class, 'verifyOtp'], $guest);
$router->post('/resend-otp', [AuthController::class, 'resendOtp'], $guest);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], $guest);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword'], $guest);
$router->get('/reset-password', [AuthController::class, 'showResetPassword'], $guest);
$router->post('/reset-password', [AuthController::class, 'resetPassword'], $guest);
$router->get('/logout', [AuthController::class, 'logout'], $auth);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'], $auth);

// Sacraments
$router->get('/sacraments', [SacramentController::class, 'index'], $auth);
$router->get('/sacraments/{slug}', [SacramentController::class, 'show'], $auth);
$router->post('/sacraments/baptism', [SacramentController::class, 'submitBaptism'], $auth);
$router->post('/sacraments/matrimony', [SacramentController::class, 'submitMatrimony'], $auth);
$router->post('/sacraments/funeral', [SacramentController::class, 'submitFuneral'], $auth);
$router->post('/sacraments/status', [SacramentController::class, 'updateStatus'], $auth);
$router->get('/api/schedules', [SacramentController::class, 'getSchedules'], $auth);

// Payments
$router->get('/payments', [PaymentController::class, 'index'], $auth);
$router->post('/payments/pay', [PaymentController::class, 'pay'], $auth);
$router->post('/payments/confirm', [PaymentController::class, 'confirm'], $auth);

// Parishes
$router->get('/parishes', [ParishController::class, 'index'], $auth);
$router->get('/parishes/{id}', [ParishController::class, 'show'], $auth);

// Schedules
$router->get('/schedules', [ScheduleController::class, 'index'], $auth);
$router->post('/schedules', [ScheduleController::class, 'store'], $auth);

// Audit
$router->get('/audit', [AuditController::class, 'index'], $auth);
