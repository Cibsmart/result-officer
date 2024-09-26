<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Download\Courses\DownloadCoursesController;
use App\Http\Controllers\Download\Courses\DownloadCoursesPageController;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsController;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsPageController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationPageController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionLevelController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionSemesterController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByRegistrationNumberController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsBySessionCourseController;
use App\Http\Controllers\Download\Results\DownloadResultByDepartmentSessionLevelController;
use App\Http\Controllers\Download\Results\DownloadResultByDepartmentSessionSemesterController;
use App\Http\Controllers\Download\Results\DownloadResultByRegistrationNumberController;
use App\Http\Controllers\Download\Results\DownloadResultBySessionCourseController;
use App\Http\Controllers\Download\Results\DownloadResultsPageController;
use App\Http\Controllers\Download\Students\DownloadStudentByRegistrationNumberController;
use App\Http\Controllers\Download\Students\DownloadStudentsByDepartmentSessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsBySessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsPageController;
use App\Http\Controllers\Imports\CancelImportEventController;
use App\Http\Controllers\Imports\ContinueImportEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\CompositeSheetController;
use App\Http\Controllers\Result\ViewStudentResultController;
use App\Http\Controllers\Summary\DepartmentResultSummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(static function (): void {
    Route::prefix('profile')->group(static function (): void {
        Route::get('', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('results')->group(static function (): void {
        Route::get('', [ViewStudentResultController::class, 'form'])->name('results.form');
        Route::post('', [ViewStudentResultController::class, 'view'])->name('results.view');
        Route::get('{student}/print', [ViewStudentResultController::class, 'print'])
            ->name('results.print');
        Route::get('{student}/transcript', [ViewStudentResultController::class, 'transcript'])
            ->name('results.transcript');
    });

    Route::prefix('summary')->group(static function (): void {
        Route::get('', [DepartmentResultSummaryController::class, 'form'])->name('summary.form');
        Route::post('', [DepartmentResultSummaryController::class, 'view'])->name('summary.view');
        Route::get('{department}/{session}/{level}', [DepartmentResultSummaryController::class, 'print'])
            ->name('summary.print');
    });

    Route::prefix('composite')->group(static function (): void {
        Route::get('', [CompositeSheetController::class, 'form'])->name('composite.form');
        Route::post('', [CompositeSheetController::class, 'view'])->name('composite.view');
        Route::get('{program}/{session}/{level}/{semester}', [CompositeSheetController::class, 'print'])
            ->name('composite.print');
    });

    Route::prefix('download/students')->group(static function (): void {
        Route::get('page', DownloadStudentsPageController::class)
            ->name('download.students.page');
        Route::post('registration-number', DownloadStudentByRegistrationNumberController::class)
            ->name('download.student.registration-number.store');
        Route::post('department-session', DownloadStudentsByDepartmentSessionController::class)
            ->name('download.students.department-session.store');
        Route::post('session', DownloadStudentsBySessionController::class)
            ->name('download.students.session.store');
    });

    Route::prefix('download/departments')->group(static function (): void {
        Route::get('page', DownloadDepartmentsPageController::class)
            ->name('download.departments.page');
        Route::post('', DownloadDepartmentsController::class)
            ->name('download.departments.store');
    });

    Route::prefix('download/courses')->group(static function (): void {
        Route::get('', DownloadCoursesPageController::class)
            ->name('download.courses.page');
        Route::post('', DownloadCoursesController::class)
            ->name('download.courses.store');
    });

    Route::prefix('import-event')->group(static function (): void {
        Route::get('cancel/{event}', CancelImportEventController::class)
            ->name('import.event.cancel');
        Route::get('continue/{event}', ContinueImportEventController::class)
            ->name('import.event.continue');
    });

    Route::prefix('download/registrations')->group(static function (): void {
        Route::get('page', DownloadRegistrationPageController::class)
            ->name('download.registrations.page');
        Route::post('registration-number', DownloadRegistrationsByRegistrationNumberController::class)
            ->name('download.registrations.registration-number.store');
        Route::post('department-session-level', DownloadRegistrationsByDepartmentSessionLevelController::class)
            ->name('download.registrations.department-session-level.store');
        Route::post('department-session-semester', DownloadRegistrationsByDepartmentSessionSemesterController::class)
            ->name('download.registrations.department-session-semester.store');
        Route::post('session-course', DownloadRegistrationsBySessionCourseController::class)
            ->name('download.registrations.session-course.store');
    });

    Route::prefix('download/results')->group(static function (): void {
        Route::get('page', DownloadResultsPageController::class)
            ->name('download.results.page');
        Route::post('registration-number', DownloadResultByRegistrationNumberController::class)
            ->name('download.results.registration-number.store');
        Route::post('department-session-level', DownloadResultByDepartmentSessionLevelController::class)
            ->name('download.results.department-session-level.store');
        Route::post('department-session-semester', DownloadResultByDepartmentSessionSemesterController::class)
            ->name('download.results.department-session-semester.store');
        Route::post('session-course', DownloadResultBySessionCourseController::class)
            ->name('download.results.session-course.store');
    });
});

require __DIR__ . '/auth.php';
