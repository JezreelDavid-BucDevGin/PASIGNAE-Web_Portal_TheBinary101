<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuditRepository;
use App\Repositories\PaymentRepository;

class PaymentService
{
    public function __construct(
        private PaymentRepository $payments = new PaymentRepository(),
        private AuditRepository $audit = new AuditRepository(),
    ) {}

    public function recordPayment(int $paymentId, string $referenceNumber, int $userId, string $ip): array
    {
        $payment = $this->payments->find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }

        if ($payment['status'] === 'paid') {
            return ['success' => false, 'message' => 'Payment already recorded.'];
        }

        $this->payments->update($paymentId, [
            'reference_number' => $referenceNumber,
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        $stmt = \App\Core\Database::pdo()->prepare(
            'INSERT INTO transactions (payment_id, description, amount) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            $paymentId,
            'GCash payment received',
            $payment['amount'],
        ]);

        $this->audit->log($userId, 'payment', 'payments', "Payment #{$paymentId} marked as paid", $ip);

        return ['success' => true, 'message' => 'Payment recorded successfully.'];
    }

    public function confirmPayment(int $paymentId, int $adminId, string $ip): array
    {
        $payment = $this->payments->find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }

        $this->payments->update($paymentId, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log($adminId, 'confirm', 'payments', "Payment #{$paymentId} confirmed by admin", $ip);

        return ['success' => true];
    }
}
