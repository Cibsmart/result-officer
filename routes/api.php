<?php

declare(strict_types=1);

use App\Http\Controllers\Api\VettingStepsApiController;

Route::get('vetting-steps/{student}', [VettingStepsApiController::class, 'index'])
    ->name('api.vetting_steps.index');
