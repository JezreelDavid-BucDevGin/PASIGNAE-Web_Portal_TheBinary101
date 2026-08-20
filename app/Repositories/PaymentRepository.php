<?php

declare(strict_types=1);

namespace App\Repositories;

class PaymentRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'payments';
    }

    public function getWithDetails(?int $parishId = null, ?int $userId = null): array
    {
        $sql = 'SELECT pay.*, sr.id AS request_id, st.name AS sacrament_name,
                       p.name AS parish_name, u.first_name, u.last_name
                FROM payments pay
                JOIN sacrament_requests sr ON sr.id = pay.request_id
                JOIN sacrament_types st ON st.id = sr.sacrament_type_id
                JOIN parishes p ON p.id = sr.parish_id
                JOIN users u ON u.id = sr.user_id
                WHERE 1=1';
        $params = [];

        if ($parishId) {
            $sql .= ' AND sr.parish_id = ?';
            $params[] = $parishId;
        }
        if ($userId) {
            $sql .= ' AND sr.user_id = ?';
            $params[] = $userId;
        }

        $sql .= ' ORDER BY pay.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTotalRevenue(?int $parishId = null): float
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) FROM payments pay
                JOIN sacrament_requests sr ON sr.id = pay.request_id
                WHERE pay.status = 'paid'";
        $params = [];

        if ($parishId) {
            $sql .= ' AND sr.parish_id = ?';
            $params[] = $parishId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function findByRequest(int $requestId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE request_id = ? LIMIT 1');
        $stmt->execute([$requestId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
