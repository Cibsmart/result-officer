<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Download\CourseRegistrations\DownloadCourseRegistrationPageController;
use App\Http\Controllers\Download\CourseRegistrations\DownloadRegistrationsByDepartmentSessionLevelController;
use App\Http\Controllers\Download\CourseRegistrations\DownloadRegistrationsByDepartmentSessionSemesterController;
use App\Http\Controllers\Download\CourseRegistrations\DownloadRegistrationsByRegistrationNumberController;
use App\Http\Controllers\Download\CourseRegistrations\DownloadRegistrationsBySessionCourseController;
use App\Http\Controllers\Download\Courses\CourseRecordController;
use App\Http\Controllers\Download\Departments\DepartmentRecordController;
use App\Http\Controllers\Download\Results\DownloadResultByDepartmentSessionLevelController;
use App\Http\Controllers\Download\Results\DownloadResultByRegistrationNumberController;
use App\Http\Controllers\Download\Results\DownloadResultsPageController;
use App\Http\Controllers\Download\Students\DownloadStudentByRegistrationNumberController;
use App\Http\Controllers\Download\Students\DownloadStudentsByDepartmentSessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsBySessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsPageController;
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

    Route::get('download/students/page', DownloadStudentsPageController::class)
        ->name('download.students.page');
    Route::post('download/student/registration-number', DownloadStudentByRegistrationNumberController::class)
        ->name('download.student.registration-number.store');
    Route::post('download/students/department-session', DownloadStudentsByDepartmentSessionController::class)
        ->name('download.students.department-session.store');
    Route::post('download/students/session', DownloadStudentsBySessionController::class)
        ->name('download.students.session.store');

    Route::get('departments', [DepartmentRecordController::class, 'getAndSaveDepartments']);
    Route::get('departments/{departmentId}', [DepartmentRecordController::class, 'getAndSaveDepartment']);

    Route::get('courses', [CourseRecordController::class, 'getAndSaveCourses']);
    Route::get('courses/{courseId}', [CourseRecordController::class, 'getAndSaveCourse']);

    Route::get('download/course-registrations/page', DownloadCourseRegistrationPageController::class)
        ->name('download.course-registrations.page');
    Route::post('download/course-registrations/registration-number',
        DownloadRegistrationsByRegistrationNumberController::class)
        ->name('download.registrations.registration-number.store');
    Route::post('download/course-registrations/department-session-level',
        DownloadRegistrationsByDepartmentSessionLevelController::class)
        ->name('download.registrations.department-session-level.store');
    Route::post('download/course-registrations/department-session-semester',
        DownloadRegistrationsByDepartmentSessionSemesterController::class)
        ->name('download.registrations.department-session-semester.store');
    Route::post('download/course-registrations/session-course',
        DownloadRegistrationsBySessionCourseController::class)
        ->name('download.registrations.session-course.store');

    Route::prefix('download/results')->group(static function (): void {
        Route::get('page', DownloadResultsPageController::class)
            ->name('download.results.page');
        Route::post('registration-number', DownloadResultByRegistrationNumberController::class)
            ->name('download.results.registration-number.store');
        Route::post('department-session-level', DownloadResultByDepartmentSessionLevelController::class)
            ->name('download.results.department-session-level.store');
        Route::post('department-session-semester', DownloadResultByDepartmentSessionLevelController::class)
            ->name('download.results.department-session-semester.store');
        Route::post('session-course', DownloadResultByDepartmentSessionLevelController::class)
            ->name('download.results.session-course.store');
    });
});

require __DIR__ . '/auth.php';
