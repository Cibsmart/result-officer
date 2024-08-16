<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class DownloadResultByDepartmentSessionSemesterController
{
    public function __invoke(Request $request): RedirectResponse
    {
        dd($request->all());
    }
}
