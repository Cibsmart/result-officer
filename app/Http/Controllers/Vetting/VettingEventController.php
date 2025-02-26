<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class VettingEventController
{
    public function index(): Response
    {
        return Inertia::render('vetting/index/page');
    }

    public function store(VettingStoreRequest $request): RedirectResponse
    {
        dd($request->validated());

    }
}
