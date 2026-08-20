<?php

declare(strict_types=1);

namespace App\Repositories;

class ScheduleRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'schedules';
    }

    public function getAvailable(int $parishId, int $sacramentTypeId): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, st.name AS sacrament_name
             FROM schedules s
             JOIN sacrament_types st ON st.id = s.sacrament_type_id
             WHERE s.parish_id = ? AND s.sacrament_type_id = ?
             AND s.status = 'available' AND s.event_date >= CURDATE()
             AND s.booked_slots < s.max_slots
             ORDER BY s.event_date, s.start_time"
        );
        $stmt->execute([$parishId, $sacramentTypeId]);
        return $stmt->fetchAll();
    }

    public function getUpcoming(int $parishId, int $sacramentTypeId): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, st.name AS sacrament_name, st.slug AS sacrament_slug
             FROM schedules s
             JOIN sacrament_types st ON st.id = s.sacrament_type_id
             WHERE s.parish_id = ? AND s.sacrament_type_id = ?
             AND s.status != 'cancelled' AND s.event_date >= CURDATE()
             ORDER BY s.event_date, s.start_time"
        );
        $stmt->execute([$parishId, $sacramentTypeId]);
        return $stmt->fetchAll();
    }

    /** Event dates that already have at least one booking (occupies the prep/wake window). */
    public function getBookedCeremonyDates(int $parishId, int $sacramentTypeId): array
    {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT event_date
             FROM schedules
             WHERE parish_id = ? AND sacrament_type_id = ?
             AND status != 'cancelled' AND booked_slots > 0
             AND event_date >= DATE_SUB(CURDATE(), INTERVAL 21 DAY)
             ORDER BY event_date"
        );
        $stmt->execute([$parishId, $sacramentTypeId]);
        return array_column($stmt->fetchAll(), 'event_date');
    }

    public function getByParish(?int $parishId = null): array
    {
        $sql = 'SELECT s.*, st.name AS sacrament_name, p.name AS parish_name
                FROM schedules s
                JOIN sacrament_types st ON st.id = s.sacrament_type_id
                JOIN parishes p ON p.id = s.parish_id';
        $params = [];

        if ($parishId) {
            $sql .= ' WHERE s.parish_id = ?';
            $params[] = $parishId;
        }

        $sql .= ' ORDER BY s.event_date DESC, s.start_time';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function incrementBooked(int $scheduleId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE schedules SET booked_slots = booked_slots + 1,
             status = IF(booked_slots + 1 >= max_slots, 'full', status)
             WHERE id = ?"
        );
        $stmt->execute([$scheduleId]);
    }
}
