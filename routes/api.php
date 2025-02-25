<?php

declare(strict_types=1);

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ExamOfficerController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\StateController;

Route::middleware(['auth:sanctum'])->group(static function (): void {
    Route::get('departments', DepartmentController::class);
    Route::get('sessions', SessionController::class);
    Route::get('states', StateController::class);
    Route::get('exam-officers', ExamOfficerController::class);
});
