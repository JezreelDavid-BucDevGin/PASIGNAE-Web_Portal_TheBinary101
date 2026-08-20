<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\AuditRepository;
use App\Repositories\PersonRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\SacramentRepository;
use App\Repositories\ScheduleRepository;

class SacramentService
{
    public function __construct(
        private SacramentRepository $sacraments = new SacramentRepository(),
        private PersonRepository $persons = new PersonRepository(),
        private ScheduleRepository $schedules = new ScheduleRepository(),
        private PaymentRepository $payments = new PaymentRepository(),
        private AuditRepository $audit = new AuditRepository(),
        private SchedulingService $scheduling = new SchedulingService(),
    ) {}

    public function submitBaptism(int $userId, array $data, string $ip): array
    {
        $type = $this->sacraments->getTypeBySlug('baptism');
        if (!$type) {
            return ['success' => false, 'message' => 'Sacrament type not found.'];
        }

        $scheduleCheck = $this->guardSchedule($data, $type);
        if (!$scheduleCheck['ok']) {
            return ['success' => false, 'message' => $scheduleCheck['message']];
        }

        Database::beginTransaction();
        try {
            $requestId = $this->sacraments->create([
                'user_id' => $userId,
                'parish_id' => $data['parish_id'],
                'sacrament_type_id' => $type['id'],
                'schedule_id' => $data['schedule_id'] ?? null,
                'status' => 'pending',
                'requested_date' => date('Y-m-d'),
                'remarks' => $data['remarks'] ?? null,
            ]);

            $childId = $this->persons->findOrCreate([
                'first_name' => $data['child_first_name'],
                'middle_name' => $data['child_middle_name'] ?? null,
                'last_name' => $data['child_last_name'],
                'birth_date' => $data['child_birth_date'],
                'gender' => $data['child_gender'] ?? null,
                'place_of_birth' => $data['child_place_of_birth'] ?? null,
            ]);

            $fatherId = $this->createPersonIfProvided($data, 'father');
            $motherId = $this->createPersonIfProvided($data, 'mother');
            $godfatherId = $this->createPersonIfProvided($data, 'godfather');
            $godmotherId = $this->createPersonIfProvided($data, 'godmother');

            $stmt = Database::pdo()->prepare(
                'INSERT INTO baptism_records (request_id, child_person_id, father_person_id, mother_person_id,
                 godfather_person_id, godmother_person_id, schedule_id, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $requestId, $childId, $fatherId, $motherId, $godfatherId, $godmotherId,
                $data['schedule_id'] ?? null, $userId,
            ]);

            if (!empty($data['schedule_id'])) {
                $this->schedules->incrementBooked((int) $data['schedule_id']);
            }

            $this->payments->create([
                'request_id' => $requestId,
                'payment_method' => 'gcash',
                'amount' => $type['fee'],
                'status' => 'pending',
            ]);

            Database::commit();
            $this->audit->log($userId, 'create', 'baptism', "Baptism request #{$requestId} submitted", $ip);

            return ['success' => true, 'request_id' => $requestId];
        } catch (\Throwable $e) {
            Database::rollBack();
            if (config('debug')) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
            return ['success' => false, 'message' => 'Failed to submit baptism request.'];
        }
    }

    public function submitMatrimony(int $userId, array $data, string $ip): array
    {
        $type = $this->sacraments->getTypeBySlug('matrimony');
        if (!$type) {
            return ['success' => false, 'message' => 'Sacrament type not found.'];
        }

        $scheduleCheck = $this->guardSchedule($data, $type);
        if (!$scheduleCheck['ok']) {
            return ['success' => false, 'message' => $scheduleCheck['message']];
        }

        Database::beginTransaction();
        try {
            $requestId = $this->sacraments->create([
                'user_id' => $userId,
                'parish_id' => $data['parish_id'],
                'sacrament_type_id' => $type['id'],
                'schedule_id' => $data['schedule_id'] ?? null,
                'status' => 'pending',
                'requested_date' => date('Y-m-d'),
                'remarks' => $data['remarks'] ?? null,
            ]);

            $groomId = $this->persons->findOrCreate([
                'first_name' => $data['groom_first_name'],
                'middle_name' => $data['groom_middle_name'] ?? null,
                'last_name' => $data['groom_last_name'],
                'birth_date' => $data['groom_birth_date'] ?? null,
                'gender' => 'male',
            ]);

            $brideId = $this->persons->findOrCreate([
                'first_name' => $data['bride_first_name'],
                'middle_name' => $data['bride_middle_name'] ?? null,
                'last_name' => $data['bride_last_name'],
                'birth_date' => $data['bride_birth_date'] ?? null,
                'gender' => 'female',
            ]);

            $stmt = Database::pdo()->prepare(
                'INSERT INTO marriage_records (request_id, groom_person_id, bride_person_id, schedule_id, created_by)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$requestId, $groomId, $brideId, $data['schedule_id'] ?? null, $userId]);

            if (!empty($data['schedule_id'])) {
                $this->schedules->incrementBooked((int) $data['schedule_id']);
            }

            $this->payments->create([
                'request_id' => $requestId,
                'payment_method' => 'gcash',
                'amount' => $type['fee'],
                'status' => 'pending',
            ]);

            Database::commit();
            $this->audit->log($userId, 'create', 'matrimony', "Matrimony request #{$requestId} submitted", $ip);

            return ['success' => true, 'request_id' => $requestId];
        } catch (\Throwable $e) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Failed to submit matrimony request.'];
        }
    }

    public function submitFuneral(int $userId, array $data, string $ip): array
    {
        $type = $this->sacraments->getTypeBySlug('funeral');
        if (!$type) {
            return ['success' => false, 'message' => 'Sacrament type not found.'];
        }

        $scheduleCheck = $this->guardSchedule($data, $type);
        if (!$scheduleCheck['ok']) {
            return ['success' => false, 'message' => $scheduleCheck['message']];
        }

        Database::beginTransaction();
        try {
            $requestId = $this->sacraments->create([
                'user_id' => $userId,
                'parish_id' => $data['parish_id'],
                'sacrament_type_id' => $type['id'],
                'schedule_id' => $data['schedule_id'] ?? null,
                'status' => 'pending',
                'requested_date' => date('Y-m-d'),
                'remarks' => $data['remarks'] ?? null,
            ]);

            $deceasedId = $this->persons->findOrCreate([
                'first_name' => $data['deceased_first_name'],
                'middle_name' => $data['deceased_middle_name'] ?? null,
                'last_name' => $data['deceased_last_name'],
                'birth_date' => $data['deceased_birth_date'] ?? null,
            ]);

            $informantId = $this->createPersonIfProvided($data, 'informant');

            $stmt = Database::pdo()->prepare(
                'INSERT INTO funeral_records (request_id, deceased_person_id, informant_person_id,
                 cause_of_death, date_of_death, time_of_death, schedule_id, funeral_location, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $requestId, $deceasedId, $informantId,
                $data['cause_of_death'] ?? null,
                $data['date_of_death'],
                $data['time_of_death'] ?? null,
                $data['schedule_id'] ?? null,
                $data['funeral_location'] ?? null,
                $userId,
            ]);

            if (!empty($data['schedule_id'])) {
                $this->schedules->incrementBooked((int) $data['schedule_id']);
            }

            $this->payments->create([
                'request_id' => $requestId,
                'payment_method' => 'gcash',
                'amount' => $type['fee'],
                'status' => 'pending',
            ]);

            Database::commit();
            $this->audit->log($userId, 'create', 'funeral', "Funeral request #{$requestId} submitted", $ip);

            return ['success' => true, 'request_id' => $requestId];
        } catch (\Throwable $e) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Failed to submit funeral request.'];
        }
    }

    public function updateStatus(int $requestId, string $status, int $adminId, string $ip): array
    {
        $request = $this->sacraments->getRequestDetail($requestId);
        if (!$request) {
            return ['success' => false, 'message' => 'Request not found.'];
        }

        $this->sacraments->update($requestId, [
            'status' => $status,
            'approved_date' => $status === 'approved' ? date('Y-m-d') : null,
        ]);

        $this->audit->log($adminId, 'update', 'sacraments', "Request #{$requestId} status changed to {$status}", $ip);

        return ['success' => true];
    }

    private function guardSchedule(array $data, array $type): array
    {
        $scheduleId = (int) ($data['schedule_id'] ?? 0);
        if ($scheduleId < 1) {
            return ['ok' => true];
        }

        return $this->scheduling->assertBookable(
            $scheduleId,
            (int) $data['parish_id'],
            (int) $type['id'],
            $type['slug']
        );
    }

    private function createPersonIfProvided(array $data, string $prefix): ?int
    {
        if (empty($data["{$prefix}_first_name"]) || empty($data["{$prefix}_last_name"])) {
            return null;
        }

        return $this->persons->findOrCreate([
            'first_name' => $data["{$prefix}_first_name"],
            'middle_name' => $data["{$prefix}_middle_name"] ?? null,
            'last_name' => $data["{$prefix}_last_name"],
            'birth_date' => $data["{$prefix}_birth_date"] ?? null,
            'phone' => $data["{$prefix}_phone"] ?? null,
            'civil_status' => $data["{$prefix}_civil_status"] ?? null,
        ]);
    }
}
