<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

final class VettingController
{
    public function index(): Response
    {
        return Inertia::render('vetting/index/page');
    }
}
