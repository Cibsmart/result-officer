<?php

declare(strict_types=1);

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ExamOfficerController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\UsersDepartmentController;
use App\Http\Controllers\Api\VettingStepController;

Route::middleware(['auth:sanctum'])->group(static function (): void {
    Route::get('departments', DepartmentController::class)
        ->middleware('permission:student.view');
    Route::get('user-departments', UsersDepartmentController::class)
        ->middleware('permission:student.view');
    Route::get('sessions', SessionController::class)
        ->middleware('permission:student.view');
    Route::get('states', StateController::class)
        ->middleware('permission:student.view');
    Route::get('exam-officers', ExamOfficerController::class)
        ->middleware('permission:student.clear');
    Route::get('student/{student}/vetting-steps', VettingStepController::class)
        ->middleware('permission:vetting.view');
});
