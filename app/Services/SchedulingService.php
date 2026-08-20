<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ScheduleRepository;

class SchedulingService
{
    public function __construct(
        private ScheduleRepository $schedules = new ScheduleRepository(),
    ) {}

    public static function rules(string $slug): array
    {
        $defaults = [
            'window_days' => 0,
            'window_label' => null,
            'ceremony_label' => 'Ceremony',
            'require_full_future_window' => false,
        ];

        return array_merge($defaults, (array) config("scheduling.{$slug}", []));
    }

    public static function windowRange(string $ceremonyDate, int $days): ?array
    {
        if ($days <= 0) {
            return null;
        }

        return [
            'start' => date('Y-m-d', strtotime($ceremonyDate . " -{$days} days")),
            'end' => date('Y-m-d', strtotime($ceremonyDate . ' -1 day')),
        ];
    }

    public function calendar(int $parishId, int $typeId, string $slug): array
    {
        $rules = self::rules($slug);
        $days = (int) $rules['window_days'];
        $upcoming = $this->schedules->getUpcoming($parishId, $typeId);
        $bookedDates = $this->schedules->getBookedCeremonyDates($parishId, $typeId);

        $dates = [];
        foreach ($upcoming as $slot) {
            $date = $slot['event_date'];
            if (!isset($dates[$date])) {
                $dates[$date] = [
                    'selectable' => false,
                    'reason' => null,
                    'window' => self::windowRange($date, $days),
                    'slots' => [],
                ];
            }

            $remaining = max(0, (int) $slot['max_slots'] - (int) $slot['booked_slots']);
            $dates[$date]['slots'][] = [
                'id' => (int) $slot['id'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'max_slots' => (int) $slot['max_slots'],
                'booked_slots' => (int) $slot['booked_slots'],
                'remaining' => $remaining,
                'is_full' => $remaining < 1 || $slot['status'] === 'full',
            ];
        }

        foreach ($dates as $date => &$info) {
            $openSlots = array_filter($info['slots'], fn(array $s) => !$s['is_full']);
            if (!$openSlots) {
                $info['selectable'] = false;
                $info['reason'] = 'Fully booked';
                continue;
            }

            $block = $this->dateBlockReason($date, $days, $rules, $bookedDates);
            if ($block) {
                $info['selectable'] = false;
                $info['reason'] = $block;
                continue;
            }

            $info['selectable'] = true;
            $info['reason'] = null;
        }
        unset($info);

        return [
            'parish_id' => $parishId,
            'window_days' => $days,
            'window_label' => $rules['window_label'],
            'ceremony_label' => $rules['ceremony_label'],
            'dates' => $dates,
        ];
    }

    public function assertBookable(int $scheduleId, int $parishId, int $typeId, string $slug): array
    {
        $slot = $this->schedules->find($scheduleId);
        if (
            !$slot
            || (int) $slot['parish_id'] !== $parishId
            || (int) $slot['sacrament_type_id'] !== $typeId
        ) {
            return ['ok' => false, 'message' => 'The selected schedule is not valid for this parish.'];
        }

        $remaining = (int) $slot['max_slots'] - (int) $slot['booked_slots'];
        if ($slot['status'] === 'cancelled' || $remaining < 1) {
            return ['ok' => false, 'message' => 'That schedule is no longer available.'];
        }

        $rules = self::rules($slug);
        $days = (int) $rules['window_days'];
        $bookedDates = $this->schedules->getBookedCeremonyDates($parishId, $typeId);
        $block = $this->dateBlockReason($slot['event_date'], $days, $rules, $bookedDates);

        if ($block) {
            return ['ok' => false, 'message' => $block];
        }

        return ['ok' => true];
    }

    private function dateBlockReason(string $ceremonyDate, int $days, array $rules, array $bookedDates): ?string
    {
        $today = date('Y-m-d');
        if ($ceremonyDate < $today) {
            return 'That date has already passed.';
        }

        if (!empty($rules['require_full_future_window']) && $days > 0) {
            $earliest = date('Y-m-d', strtotime("+{$days} days"));
            if ($ceremonyDate < $earliest) {
                return "This sacrament requires a {$days}-day period before the ceremony. Please choose a later date.";
            }
        }

        if ($days > 0 && $this->windowOverlapsBookings($ceremonyDate, $days, $bookedDates)) {
            $label = strtolower((string) ($rules['window_label'] ?? 'required period'));
            return "This date conflicts with an existing {$label} at this parish.";
        }

        return null;
    }

    /**
     * Occupancy is the ceremony date plus the preceding window (prep/wake).
     * Same-day bookings are handled by slot capacity, not this check.
     */
    private function windowOverlapsBookings(string $ceremonyDate, int $days, array $bookedDates): bool
    {
        $start = date('Y-m-d', strtotime($ceremonyDate . " -{$days} days"));
        $end = $ceremonyDate;

        foreach ($bookedDates as $other) {
            if ($other === $ceremonyDate) {
                continue;
            }
            $otherStart = date('Y-m-d', strtotime($other . " -{$days} days"));
            $otherEnd = $other;
            if ($start <= $otherEnd && $otherStart <= $end) {
                return true;
            }
        }

        return false;
    }
}
