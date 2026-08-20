<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ParishRepository;
use App\Repositories\SacramentRepository;

class HomeController extends Controller
{
    public function index(): void
    {
        $parishes = (new ParishRepository())->getAllWithVicariate();
        $sacraments = (new SacramentRepository())->getTypes();

        view('home.index', [
            'layout' => 'guest',
            'title' => 'Welcome',
            'parishes' => $parishes,
            'sacraments' => $sacraments,
        ]);
    }
}
