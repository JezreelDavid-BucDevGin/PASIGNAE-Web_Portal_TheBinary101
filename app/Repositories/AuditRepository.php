<?php

declare(strict_types=1);

namespace App\Repositories;

class AuditRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'audit_logs';
    }

    public function log(?int $userId, string $action, string $module, ?string $description, string $ip): void
    {
        $this->create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => $ip,
        ]);
    }

    public function getRecent(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT al.*, u.first_name, u.last_name, u.email
             FROM audit_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
