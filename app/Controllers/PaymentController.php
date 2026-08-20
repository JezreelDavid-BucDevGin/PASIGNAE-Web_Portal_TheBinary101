<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PaymentRepository;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    private PaymentService $service;
    private PaymentRepository $payments;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PaymentService();
        $this->payments = new PaymentRepository();
    }

    public function index(): void
    {
        $user = auth();
        $parishId = in_array($user['role_slug'], ['parish_admin', 'parish_staff'], true)
            ? (int) $user['parish_id'] : null;
        $userId = $user['role_slug'] === 'parishioner' ? (int) $user['id'] : null;

        view('payments.index', [
            'layout' => 'app',
            'title' => 'Payments',
            'payments' => $this->payments->getWithDetails($parishId, $userId),
            'total_revenue' => $this->payments->getTotalRevenue($parishId),
        ]);
    }

    public function pay(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/payments');
        }

        $result = $this->service->recordPayment(
            (int) $this->request->input('payment_id'),
            $this->request->input('reference_number'),
            (int) auth()['id'],
            $this->request->ip()
        );

        flash($result['success'] ? 'success' : 'error', $result['message']);
        redirect('/payments');
    }

    public function confirm(): void
    {
        if (!$this->requireCsrf()) {
            redirect('/payments');
        }

        $result = $this->service->confirmPayment(
            (int) $this->request->input('payment_id'),
            (int) auth()['id'],
            $this->request->ip()
        );

        flash($result['success'] ? 'success' : 'error', $result['message'] ?? 'Payment confirmed.');
        redirect('/payments');
    }
}
