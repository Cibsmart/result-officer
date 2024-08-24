<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use Inertia\Inertia;
use Inertia\Response;

final readonly class CompositeSheetController
{
    public function form(): Response
    {
        return Inertia::render('reports/composite/form/page');
    }

    public function view(): Response
    {
        return Inertia::render('reports/composite/view/page');
    }
}
