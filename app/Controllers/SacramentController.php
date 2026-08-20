<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ParishRepository;
use App\Repositories\SacramentRepository;
use App\Services\SacramentService;
use App\Services\SchedulingService;

class SacramentController extends Controller
{
    private SacramentService $service;
    private SacramentRepository $sacraments;

    public function __construct()
    {
        parent::__construct();
        $this->service = new SacramentService();
        $this->sacraments = new SacramentRepository();
    }

    public function index(): void
    {
        $user = auth();
        $parishId = in_array($user['role_slug'], ['parish_admin', 'parish_staff', 'parish_priest'], true)
            ? (int) $user['parish_id'] : null;
        $userId = $user['role_slug'] === 'parishioner' ? (int) $user['id'] : null;

        view('sacraments.index', [
            'layout' => 'app',
            'title' => 'Sacrament Requests',
            'requests' => $this->sacraments->getRequestsWithDetails($parishId, $userId),
            'types' => $this->sacraments->getTypes(),
        ]);
    }

    public function show(string $slug): void
    {
        $allowed = ['baptism', 'matrimony', 'funeral'];
        if (!in_array($slug, $allowed, true)) {
            flash('error', 'Sacrament not found.');
            redirect('/sacraments');
        }

        $type = $this->sacraments->getTypeBySlug($slug);
        if (!$type) {
            flash('error', 'Sacrament not found.');
            redirect('/sacraments');
        }

        $parishes = (new ParishRepository())->getAllWithVicariate();

        view("sacraments.{$slug}", [
            'layout' => 'app',
            'title' => $type['name'] . ' Application',
            'type' => $type,
            'parishes' => $parishes,
        ]);
    }

    public function submitBaptism(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/sacraments/baptism');
        }

        $result = $this->service->submitBaptism((int) auth()['id'], $this->request->all(), $this->request->ip());

        flash($result['success'] ? 'success' : 'error', $result['success']
            ? 'Baptism request submitted successfully!'
            : ($result['message'] ?? 'Submission failed.'));
        redirect($result['success'] ? '/sacraments' : '/sacraments/baptism');
    }

    public function submitMatrimony(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/sacraments/matrimony');
        }

        $result = $this->service->submitMatrimony((int) auth()['id'], $this->request->all(), $this->request->ip());

        flash($result['success'] ? 'success' : 'error', $result['success']
            ? 'Matrimony request submitted successfully!'
            : ($result['message'] ?? 'Submission failed.'));
        redirect($result['success'] ? '/sacraments' : '/sacraments/matrimony');
    }

    public function submitFuneral(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/sacraments/funeral');
        }

        $result = $this->service->submitFuneral((int) auth()['id'], $this->request->all(), $this->request->ip());

        flash($result['success'] ? 'success' : 'error', $result['success']
            ? 'Funeral request submitted successfully!'
            : ($result['message'] ?? 'Submission failed.'));
        redirect($result['success'] ? '/sacraments' : '/sacraments/funeral');
    }

    public function updateStatus(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/sacraments');
        }

        $result = $this->service->updateStatus(
            (int) $this->request->input('request_id'),
            $this->request->input('status'),
            (int) auth()['id'],
            $this->request->ip()
        );

        flash($result['success'] ? 'success' : 'error', $result['message'] ?? 'Status updated.');
        redirect('/sacraments');
    }

    public function getSchedules(): void
    {
        header('Content-Type: application/json');
        $parishId = (int) $this->request->input('parish_id');
        $typeId = (int) $this->request->input('sacrament_type_id');

        $type = $this->sacraments->getTypeById($typeId);

        if ($parishId < 1 || !$type) {
            echo json_encode(['dates' => new \stdClass(), 'window_days' => 0]);
            exit;
        }

        $payload = (new SchedulingService())->calendar($parishId, $typeId, $type['slug']);
        $payload['sacrament'] = [
            'id' => (int) $type['id'],
            'slug' => $type['slug'],
            'name' => $type['name'],
        ];

        echo json_encode($payload);
        exit;
    }
}
