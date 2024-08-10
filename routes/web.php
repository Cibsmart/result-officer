<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Download\Courses\CourseRecordController;
use App\Http\Controllers\Download\Departments\DepartmentRecordController;
use App\Http\Controllers\Download\Students\DownloadStudentRegistrationNumberController;
use App\Http\Controllers\Download\Students\DownloadStudentsDepartmentSessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsPageController;
use App\Http\Controllers\Download\Students\DownloadStudentsSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Result\ViewStudentResultController;
use App\Http\Controllers\Summary\DepartmentResultSummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware(['auth'])->name('dashboard');

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

    Route::get('download/students/page', [DownloadStudentsPageController::class, 'index'])
        ->name('download.students.page');

    Route::get('download/student/registration-number', [DownloadStudentRegistrationNumberController::class, 'create'])
        ->name('download.student.registration-number.form');
    Route::post('download/student/registration-number', [DownloadStudentRegistrationNumberController::class, 'store'])
        ->name('download.student.registration-number.store');

    Route::post('download/students/department-session', [DownloadStudentsDepartmentSessionController::class, 'store'])
        ->name('download.students.department-session.store');

    Route::post('download/students/session', [DownloadStudentsSessionController::class, 'store'])
        ->name('download.students.session.store');

    Route::get('departments', [DepartmentRecordController::class, 'getAndSaveDepartments']);
    Route::get('departments/{departmentId}', [DepartmentRecordController::class, 'getAndSaveDepartment']);

    Route::get('courses', [CourseRecordController::class, 'getAndSaveCourses']);
    Route::get('courses/{courseId}', [CourseRecordController::class, 'getAndSaveCourse']);
});

require __DIR__ . '/auth.php';
