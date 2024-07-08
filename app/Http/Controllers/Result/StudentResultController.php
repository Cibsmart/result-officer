<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use App\Http\Requests\Results\ResultRequest;
use Inertia\Inertia;
use Inertia\Response;

final class StudentResultController extends Controller
{
    public function page(): Response
    {
        return Inertia::render('result/view/page');
    }

    public function results(ResultRequest $request)
    {
        $input = $request->input('matriculation_number');
        dd($input);
    }
}
