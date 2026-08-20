<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuditRepository;
use App\Repositories\ParishRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\SacramentRepository;
use App\Repositories\UserRepository;

class DashboardService
{
    public function __construct(
        private SacramentRepository $sacraments = new SacramentRepository(),
        private PaymentRepository $payments = new PaymentRepository(),
        private ParishRepository $parishes = new ParishRepository(),
        private UserRepository $users = new UserRepository(),
        private AuditRepository $audit = new AuditRepository(),
    ) {}

    public function getDioceseStats(): array
    {
        return [
            'total_parishes' => $this->parishes->countAll(),
            'total_users' => count($this->users->all()),
            'requests_by_status' => $this->sacraments->countByStatus(),
            'requests_by_type' => $this->sacraments->countByType(),
            'total_revenue' => $this->payments->getTotalRevenue(),
            'recent_activity' => $this->audit->getRecent(10),
            'recent_requests' => array_slice($this->sacraments->getRequestsWithDetails(), 0, 10),
        ];
    }

    public function getParishStats(int $parishId): array
    {
        return [
            'requests_by_status' => $this->sacraments->countByStatus($parishId),
            'requests_by_type' => $this->sacraments->countByType($parishId),
            'total_revenue' => $this->payments->getTotalRevenue($parishId),
            'recent_requests' => array_slice($this->sacraments->getRequestsWithDetails($parishId), 0, 10),
            'recent_payments' => array_slice($this->payments->getWithDetails($parishId), 0, 5),
        ];
    }

    public function getUserStats(int $userId): array
    {
        return [
            'my_requests' => $this->sacraments->getRequestsWithDetails(null, $userId),
            'my_payments' => $this->payments->getWithDetails(null, $userId),
        ];
    }
}
