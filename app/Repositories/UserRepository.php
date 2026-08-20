<?php

declare(strict_types=1);

namespace App\Repositories;

class UserRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'users';
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug, p.name AS parish_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN parishes p ON p.id = u.parish_id
             WHERE u.email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findWithRole(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug, p.name AS parish_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN parishes p ON p.id = u.parish_id
             WHERE u.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAllWithRoles(): array
    {
        $stmt = $this->db->query(
            'SELECT u.*, r.name AS role_name, r.slug AS role_slug, p.name AS parish_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN parishes p ON p.id = u.parish_id
             ORDER BY u.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function countByStatus(): array
    {
        $stmt = $this->db->query(
            "SELECT status, COUNT(*) AS count FROM users GROUP BY status"
        );
        return $stmt->fetchAll();
    }
}
