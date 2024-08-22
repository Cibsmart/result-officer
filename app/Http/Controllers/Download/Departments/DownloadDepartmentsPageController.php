<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadDepartmentsPageController
{
    public function __invoke(): Response
    {
        return Inertia::render('download/departments/page');
    }
}
