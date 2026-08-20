<?php

declare(strict_types=1);

namespace App\Repositories;

class ParishRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'parishes';
    }

    public function getAllWithVicariate(): array
    {
        $stmt = $this->db->query(
            'SELECT p.*, v.name AS vicariate_name,
                    CONCAT_WS(", ", a.street, a.barangay, a.city, a.province) AS full_address
             FROM parishes p
             JOIN vicariates v ON v.id = p.vicariate_id
             LEFT JOIN addresses a ON a.id = p.address_id
             WHERE p.status = "active"
             ORDER BY v.name, p.name'
        );
        return $stmt->fetchAll();
    }

    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, v.name AS vicariate_name,
                    a.street, a.barangay, a.city, a.province, a.region, a.zip_code
             FROM parishes p
             JOIN vicariates v ON v.id = p.vicariate_id
             LEFT JOIN addresses a ON a.id = p.address_id
             WHERE p.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getVicariates(): array
    {
        return $this->db->query('SELECT * FROM vicariates ORDER BY name')->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM parishes WHERE status = "active"')->fetchColumn();
    }
}
