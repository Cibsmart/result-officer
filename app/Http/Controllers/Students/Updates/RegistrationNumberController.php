<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use Illuminate\Http\Request;

final class RegistrationNumberController
{
    public function __invoke(Request $request): void
    {
        dd($request->all());
    }
}
