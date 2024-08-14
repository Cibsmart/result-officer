<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

final readonly class DashboardController
{
    public function __invoke(): Response
    {
        return Inertia::render('dashboard');
    }
}
