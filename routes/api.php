<?php

declare(strict_types=1);

use App\Http\Controllers\Api\VettingStepsApiController;

Route::middleware(['auth:sanctum'])->group(static function (): void {
    Route::get('vetting-steps/{student}', [VettingStepsApiController::class, 'index'])
        ->name('api.vetting_steps.index');
});
