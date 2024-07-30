<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Result\ViewStudentResultController;
use App\Http\Controllers\Summary\DepartmentResultSummaryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', static fn () => Inertia::render('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(static function (): void {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('results', [ViewStudentResultController::class, 'form'])->name('results.form');
    Route::post('results', [ViewStudentResultController::class, 'view'])->name('results.view');
    Route::get('results/{student}/print', [ViewStudentResultController::class, 'print'])
        ->name('results.print');
    Route::get('results/{student}/transcript', [ViewStudentResultController::class, 'transcript'])
        ->name('results.transcript');

    Route::get('summary', [DepartmentResultSummaryController::class, 'form'])->name('summary.form');
    Route::post('summary', [DepartmentResultSummaryController::class, 'view'])->name('summary.view');
    Route::get('summary/{department}/{session}/{level}', [DepartmentResultSummaryController::class, 'print'])
        ->name('summary.print');
});

require __DIR__ . '/auth.php';
