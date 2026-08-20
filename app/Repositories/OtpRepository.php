<?php

declare(strict_types=1);

namespace App\Repositories;

class OtpRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'otp_verifications';
    }

    public function createOtp(int $userId, string $code, string $purpose, int $expiryMinutes): int
    {
        $this->invalidateExisting($userId, $purpose);
        return $this->create([
            'user_id' => $userId,
            'otp_code' => $code,
            'purpose' => $purpose,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$expiryMinutes} minutes")),
        ]);
    }

    public function verify(int $userId, string $code, string $purpose): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM otp_verifications
             WHERE user_id = ? AND otp_code = ? AND purpose = ?
             AND verified_at IS NULL AND expires_at > NOW()
             LIMIT 1"
        );
        $stmt->execute([$userId, $code, $purpose]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $update = $this->db->prepare('UPDATE otp_verifications SET verified_at = NOW() WHERE id = ?');
        $update->execute([$row['id']]);
        return true;
    }

    private function invalidateExisting(int $userId, string $purpose): void
    {
        $stmt = $this->db->prepare(
            "UPDATE otp_verifications SET verified_at = NOW()
             WHERE user_id = ? AND purpose = ? AND verified_at IS NULL"
        );
        $stmt->execute([$userId, $purpose]);
    }
}
