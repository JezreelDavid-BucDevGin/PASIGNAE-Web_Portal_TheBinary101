<?php

declare(strict_types=1);

namespace App\Repositories;

class PersonRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'persons';
    }

    public function findOrCreate(array $data): int
    {
        if (!empty($data['id'])) {
            return (int) $data['id'];
        }

        $stmt = $this->db->prepare(
            'SELECT id FROM persons
             WHERE first_name = ? AND last_name = ?
             AND (birth_date = ? OR birth_date IS NULL)
             LIMIT 1'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['birth_date'] ?? null,
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            return (int) $existing['id'];
        }

        return $this->create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'nationality' => $data['nationality'] ?? 'Filipino',
            'civil_status' => $data['civil_status'] ?? null,
            'place_of_birth' => $data['place_of_birth'] ?? null,
        ]);
    }
}
