<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\AuditRepository;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Helpers\Mailer;

class AuthService
{
    public function __construct(
        private UserRepository $users = new UserRepository(),
        private OtpRepository $otp = new OtpRepository(),
        private AuditRepository $audit = new AuditRepository(),
    ) {}

    public function login(string $email, string $password, string $ip): array
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if ($user['status'] === 'inactive') {
            return ['success' => false, 'message' => 'Your account has been deactivated.'];
        }

        if (!$user['email_verified_at']) {
            return ['success' => false, 'message' => 'Please verify your email before logging in.', 'needs_verification' => true, 'user_id' => $user['id']];
        }

        $this->setSession($user);
        $this->audit->log((int) $user['id'], 'login', 'auth', 'User logged in', $ip);

        return ['success' => true, 'user' => $user];
    }

    public function register(array $data, string $ip): array
    {
        if ($this->users->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email is already registered.'];
        }

        $userId = $this->users->create([
            'role_id' => config('roles.parishioner'),
            'parish_id' => $data['parish_id'] ?? null,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'status' => 'pending',
        ]);

        $otpCode = $this->sendOtp((int) $userId, 'registration');
        $this->audit->log($userId, 'register', 'auth', 'New user registered', $ip);

        return ['success' => true, 'user_id' => $userId, 'otp_sent' => $otpCode !== null];
    }

    public function verifyOtp(int $userId, string $code, string $purpose = 'registration'): array
    {
        if (!$this->otp->verify($userId, $code, $purpose)) {
            return ['success' => false, 'message' => 'Invalid or expired OTP code.'];
        }

        if ($purpose === 'registration') {
            $this->users->update($userId, [
                'email_verified_at' => date('Y-m-d H:i:s'),
                'status' => 'active',
            ]);
        }

        return ['success' => true];
    }

    public function sendOtp(int $userId, string $purpose): ?string
    {
        $user = $this->users->findWithRole($userId);
        if (!$user) {
            return null;
        }

        $code = str_pad((string) random_int(0, 999999), config('otp.length'), '0', STR_PAD_LEFT);
        $this->otp->createOtp($userId, $code, $purpose, config('otp.expiry_minutes'));

        $sent = Mailer::sendOtp($user['email'], $user['first_name'], $code, $purpose);

        return $sent ? $code : null;
    }

    public function logout(string $ip): void
    {
        $user = auth();
        if ($user) {
            $this->audit->log((int) $user['id'], 'logout', 'auth', 'User logged out', $ip);
        }
        \App\Core\Session::destroy();
    }

    public function requestPasswordReset(string $email): array
    {
        $user = $this->users->findByEmail($email);
        if (!$user) {
            return ['success' => true, 'message' => 'If that email exists, a reset link has been sent.'];
        }

        $token = bin2hex(random_bytes(32));
        $stmt = Database::pdo()->prepare(
            'INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([$email, $token, date('Y-m-d H:i:s', strtotime('+1 hour'))]);

        Mailer::sendPasswordReset($email, $user['first_name'], $token);

        return ['success' => true, 'message' => 'If that email exists, a reset link has been sent.'];
    }

    public function resetPassword(string $token, string $password): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT * FROM password_resets WHERE token = ? AND used_at IS NULL AND expires_at > NOW() LIMIT 1"
        );
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            return ['success' => false, 'message' => 'Invalid or expired reset token.'];
        }

        $user = $this->users->findByEmail($reset['email']);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        Database::beginTransaction();
        try {
            $this->users->update((int) $user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $update = Database::pdo()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?');
            $update->execute([$reset['id']]);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Failed to reset password.'];
        }

        return ['success' => true, 'message' => 'Password has been reset successfully.'];
    }

    private function setSession(array $user): void
    {
        unset($user['password']);
        $_SESSION['user'] = $user;
    }
}
