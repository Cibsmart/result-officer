<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

final class ResultController extends Controller
{
    public function view(): Response
    {
        return Inertia::render('result/view/page');
    }
}
