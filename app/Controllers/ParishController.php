<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ParishRepository;

class ParishController extends Controller
{
    public function index(): void
    {
        $repo = new ParishRepository();
        view('parishes.index', [
            'layout' => 'app',
            'title' => 'Parish Management',
            'parishes' => $repo->getAllWithVicariate(),
            'vicariates' => $repo->getVicariates(),
        ]);
    }

    public function show(string $id): void
    {
        $parish = (new ParishRepository())->findWithDetails((int) $id);
        if (!$parish) {
            flash('error', 'Parish not found.');
            redirect('/parishes');
        }

        view('parishes.show', [
            'layout' => 'app',
            'title' => $parish['name'],
            'parish' => $parish,
        ]);
    }
}
