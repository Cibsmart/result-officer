<?php

declare(strict_types=1);

use App\Http\Controllers\Api\DepartmentController;

Route::middleware(['auth:sanctum'])->group(static function (): void {
    Route::get('departments', DepartmentController::class);
});
