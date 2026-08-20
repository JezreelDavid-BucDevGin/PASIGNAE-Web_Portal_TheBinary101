<?php

declare(strict_types=1);

namespace App\Repositories;

class SacramentRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'sacrament_requests';
    }

    public function getTypes(): array
    {
        return $this->db->query('SELECT * FROM sacrament_types ORDER BY name')->fetchAll();
    }

    public function getTypeBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM sacrament_types WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getTypeById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM sacrament_types WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getRequestsWithDetails(?int $parishId = null, ?int $userId = null): array
    {
        $sql = 'SELECT sr.*, st.name AS sacrament_name, st.slug AS sacrament_slug, st.fee,
                       p.name AS parish_name, u.first_name, u.last_name, u.email,
                       sch.event_date, sch.start_time
                FROM sacrament_requests sr
                JOIN sacrament_types st ON st.id = sr.sacrament_type_id
                JOIN parishes p ON p.id = sr.parish_id
                JOIN users u ON u.id = sr.user_id
                LEFT JOIN schedules sch ON sch.id = sr.schedule_id
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

        $sql .= ' ORDER BY sr.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRequestDetail(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT sr.*, st.name AS sacrament_name, st.slug AS sacrament_slug, st.fee,
                    p.name AS parish_name, u.first_name, u.last_name, u.email, u.phone,
                    sch.event_date, sch.start_time, sch.end_time
             FROM sacrament_requests sr
             JOIN sacrament_types st ON st.id = sr.sacrament_type_id
             JOIN parishes p ON p.id = sr.parish_id
             JOIN users u ON u.id = sr.user_id
             LEFT JOIN schedules sch ON sch.id = sr.schedule_id
             WHERE sr.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countByStatus(?int $parishId = null): array
    {
        $sql = 'SELECT status, COUNT(*) AS count FROM sacrament_requests';
        $params = [];

        if ($parishId) {
            $sql .= ' WHERE parish_id = ?';
            $params[] = $parishId;
        }

        $sql .= ' GROUP BY status';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countByType(?int $parishId = null): array
    {
        $sql = 'SELECT st.name, COUNT(sr.id) AS count
                FROM sacrament_types st
                LEFT JOIN sacrament_requests sr ON sr.sacrament_type_id = st.id';
        $params = [];

        if ($parishId) {
            $sql .= ' AND sr.parish_id = ?';
            $params[] = $parishId;
        }

        $sql .= ' GROUP BY st.id, st.name ORDER BY st.id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
