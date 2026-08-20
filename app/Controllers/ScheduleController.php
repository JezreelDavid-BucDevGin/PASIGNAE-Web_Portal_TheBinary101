<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ParishRepository;
use App\Repositories\SacramentRepository;
use App\Repositories\ScheduleRepository;

class ScheduleController extends Controller
{
    public function index(): void
    {
        $user = auth();
        $parishId = in_array($user['role_slug'], ['parish_admin', 'parish_staff', 'parish_priest'], true)
            ? (int) $user['parish_id'] : null;

        view('schedules.index', [
            'layout' => 'app',
            'title' => 'Schedules',
            'schedules' => (new ScheduleRepository())->getByParish($parishId),
            'parishes' => (new ParishRepository())->getAllWithVicariate(),
            'sacrament_types' => (new SacramentRepository())->getTypes(),
        ]);
    }

    public function store(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/schedules');
        }

        (new ScheduleRepository())->create([
            'parish_id' => $this->request->input('parish_id'),
            'sacrament_type_id' => $this->request->input('sacrament_type_id'),
            'event_date' => $this->request->input('event_date'),
            'start_time' => $this->request->input('start_time'),
            'end_time' => $this->request->input('end_time') ?: null,
            'max_slots' => $this->request->input('max_slots') ?: 5,
            'status' => 'available',
        ]);

        flash('success', 'Schedule created successfully.');
        redirect('/schedules');
    }
}
