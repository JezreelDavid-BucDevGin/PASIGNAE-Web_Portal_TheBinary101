<?php

declare(strict_types=1);

namespace App\Helpers;

class Mailer
{
    public static function sendOtp(string $email, string $name, string $code, string $purpose): bool
    {
        $subject = match ($purpose) {
            'registration' => 'Verify Your PASIGNAE Account',
            'login' => 'Your PASIGNAE Login OTP',
            'password_reset' => 'Your PASIGNAE Password Reset OTP',
            default => 'Your PASIGNAE Verification Code',
        };

        $body = self::template("
            <h2>Peace be with you, {$name}!</h2>
            <p>Your verification code is:</p>
            <div style='font-size:32px;font-weight:bold;letter-spacing:8px;color:#7c2d3e;padding:20px;background:#faf8f5;text-align:center;border-radius:8px;'>{$code}</div>
            <p>This code expires in " . config('otp.expiry_minutes') . " minutes.</p>
            <p><em>Diocese of Pasig — PASIGNAE</em></p>
        ");

        return self::send($email, $subject, $body);
    }

    public static function sendPasswordReset(string $email, string $name, string $token): bool
    {
        $url = base_url("reset-password?token={$token}");
        $body = self::template("
            <h2>Peace be with you, {$name}!</h2>
            <p>Click the button below to reset your password:</p>
            <p><a href='{$url}' style='display:inline-block;padding:12px 24px;background:#7c2d3e;color:#fff;text-decoration:none;border-radius:6px;'>Reset Password</a></p>
            <p>This link expires in 1 hour.</p>
        ");

        return self::send($email, 'Reset Your PASIGNAE Password', $body);
    }

    private static function send(string $to, string $subject, string $body): bool
    {
        $mailConfig = config('mail');
        $useSmtp = !empty($mailConfig['username']);

        if (!$useSmtp) {
            self::log("LOG-ONLY | To: {$to} | Subject: {$subject}\n{$body}");
            return true;
        }

        $autoload = BASE_PATH . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            self::log("FAILED | PHPMailer is not installed. Run: ddev composer install");
            return false;
        }

        require_once $autoload;

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $mailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailConfig['username'];
            $mail->Password = preg_replace('/\s+/', '', (string) $mailConfig['password']);
            $mail->SMTPSecure = $mailConfig['encryption'];
            $mail->Port = (int) $mailConfig['port'];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
            self::log("SENT | To: {$to} | Subject: {$subject}");
            return true;
        } catch (\Exception $e) {
            $error = $mail->ErrorInfo ?: $e->getMessage();
            self::log("FAILED | To: {$to} | {$error}");
            return false;
        }
    }

    private static function log(string $message): void
    {
        $logPath = STORAGE_PATH . '/logs/mail.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }
        file_put_contents($logPath, date('Y-m-d H:i:s') . " | {$message}\n---\n", FILE_APPEND);
    }

    private static function template(string $content): string
    {
        return "<!DOCTYPE html><html><body style='font-family:Georgia,serif;color:#1e3a5f;max-width:600px;margin:0 auto;padding:20px;'>{$content}</body></html>";
    }
}
