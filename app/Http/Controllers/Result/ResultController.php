<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ResultController extends Controller
{
    public function view()
    {
        return Inertia::render('result/view/page');
    }
}
