<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ParishRepository;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthService();
    }

    public function showLogin(): void
    {
        view('auth.login', ['layout' => 'guest', 'title' => 'Sign In']);
    }

    public function login(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/login');
        }

        if (!$this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ])) {
            redirect('/login');
        }

        $result = $this->auth->login(
            $this->request->input('email'),
            $this->request->input('password'),
            $this->request->ip()
        );

        if (!$result['success']) {
            if (!empty($result['needs_verification'])) {
                flash('info', $result['message']);
                redirect('/verify-otp?user_id=' . $result['user_id']);
            }
            flash('error', $result['message']);
            redirect('/login');
        }

        flash('success', 'Welcome back, ' . auth()['first_name'] . '!');
        redirect('/dashboard');
    }

    public function showRegister(): void
    {
        $parishes = (new ParishRepository())->getAllWithVicariate();
        view('auth.register', [
            'layout' => 'guest',
            'title' => 'Create Account',
            'parishes' => $parishes,
        ]);
    }

    public function register(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/register');
        }

        if (!$this->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:200',
            'password' => 'required|min:8|confirmed',
        ])) {
            redirect('/register');
        }

        $result = $this->auth->register([
            'first_name' => $this->request->input('first_name'),
            'middle_name' => $this->request->input('middle_name'),
            'last_name' => $this->request->input('last_name'),
            'email' => $this->request->input('email'),
            'phone' => $this->request->input('phone'),
            'parish_id' => $this->request->input('parish_id') ?: null,
            'password' => $this->request->input('password'),
        ], $this->request->ip());

        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/register');
        }

        if (!empty($result['otp_sent'])) {
            flash('success', 'Account created! Please verify your email with the OTP sent.');
        } else {
            flash('error', 'Account created, but the OTP email could not be sent. Check storage/logs/mail.log or use Resend OTP.');
        }
        redirect('/verify-otp?user_id=' . $result['user_id']);
    }

    public function showVerifyOtp(): void
    {
        $userId = (int) $this->request->input('user_id');
        view('auth.verify-otp', [
            'layout' => 'guest',
            'title' => 'Verify Email',
            'user_id' => $userId,
        ]);
    }

    public function verifyOtp(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/verify-otp');
        }

        $userId = (int) $this->request->input('user_id');
        $code = $this->request->input('otp_code');

        $result = $this->auth->verifyOtp($userId, $code, 'registration');

        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/verify-otp?user_id=' . $userId);
        }

        flash('success', 'Email verified! You can now sign in.');
        redirect('/login');
    }

    public function resendOtp(): void
    {
        $userId = (int) $this->request->input('user_id');
        $sent = $this->auth->sendOtp($userId, 'registration');
        if ($sent) {
            flash('success', 'A new OTP has been sent to your email.');
        } else {
            flash('error', 'Could not send the OTP email. Check storage/logs/mail.log.');
        }
        redirect('/verify-otp?user_id=' . $userId);
    }

    public function showForgotPassword(): void
    {
        view('auth.forgot-password', ['layout' => 'guest', 'title' => 'Forgot Password']);
    }

    public function forgotPassword(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/forgot-password');
        }

        $result = $this->auth->requestPasswordReset($this->request->input('email'));
        flash('success', $result['message']);
        redirect('/login');
    }

    public function showResetPassword(): void
    {
        view('auth.reset-password', [
            'layout' => 'guest',
            'title' => 'Reset Password',
            'token' => $this->request->input('token'),
        ]);
    }

    public function resetPassword(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/reset-password');
        }

        $result = $this->auth->resetPassword(
            $this->request->input('token'),
            $this->request->input('password')
        );

        flash($result['success'] ? 'success' : 'error', $result['message']);
        redirect('/login');
    }

    public function logout(): void
    {
        $this->auth->logout($this->request->ip());
        redirect('/');
    }
}
