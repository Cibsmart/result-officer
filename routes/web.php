<?php

declare(strict_types=1);

use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Download\Courses\DownloadCoursesController;
use App\Http\Controllers\Download\Courses\DownloadCoursesPageController;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsController;
use App\Http\Controllers\Download\Departments\DownloadDepartmentsPageController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationPageController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionLevelController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByDepartmentSessionSemesterController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsByRegistrationNumberController;
use App\Http\Controllers\Download\Registrations\DownloadRegistrationsBySessionCourseController;
use App\Http\Controllers\Download\Results\DownloadResultByDepartmentSessionLevelController;
use App\Http\Controllers\Download\Results\DownloadResultByDepartmentSessionSemesterController;
use App\Http\Controllers\Download\Results\DownloadResultByRegistrationNumberController;
use App\Http\Controllers\Download\Results\DownloadResultBySessionCourseController;
use App\Http\Controllers\Download\Results\DownloadResultsByDepartmentSessionController;
use App\Http\Controllers\Download\Results\DownloadResultsPageController;
use App\Http\Controllers\Download\Students\DownloadStudentByRegistrationNumberController;
use App\Http\Controllers\Download\Students\DownloadStudentsByDepartmentSessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsBySessionController;
use App\Http\Controllers\Download\Students\DownloadStudentsPageController;
use App\Http\Controllers\Exports\Results\ExportResultsByDepartmentSessionController;
use App\Http\Controllers\Exports\Results\ExportResultsByRegistrationNumberController;
use App\Http\Controllers\Exports\Results\ExportResultsPageController;
use App\Http\Controllers\Exports\Results\RegistrationNumberListResultsExportController;
use App\Http\Controllers\FinalResults\StudentFinalResultController;
use App\Http\Controllers\Imports\CancelImportEventController;
use App\Http\Controllers\Imports\ContinueImportEventController;
use App\Http\Controllers\Imports\FinalResultImportController;
use App\Http\Controllers\Imports\ProgramCurriculumImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\CompositeSheetController;
use App\Http\Controllers\Reports\DepartmentClearedController;
use App\Http\Controllers\Results\ViewStudentResultController;
use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Students\Updates\BirthDateUpdateController;
use App\Http\Controllers\Students\Updates\EntryLevelUpdateController;
use App\Http\Controllers\Students\Updates\EntryModeUpdateController;
use App\Http\Controllers\Students\Updates\EntrySessionUpdateController;
use App\Http\Controllers\Students\Updates\GenderUpdateController;
use App\Http\Controllers\Students\Updates\JambRegistrationNumberUpdateController;
use App\Http\Controllers\Students\Updates\LocalGovernmentUpdateController;
use App\Http\Controllers\Students\Updates\ProgramUpdateController;
use App\Http\Controllers\Students\Updates\RegistrationNumberController;
use App\Http\Controllers\Students\Updates\ResultUpdateController;
use App\Http\Controllers\Students\Updates\StudentEmailUpdateController;
use App\Http\Controllers\Students\Updates\StudentNameController;
use App\Http\Controllers\Students\Updates\StudentPhoneNumberUpdateController;
use App\Http\Controllers\Students\Updates\StudentStatusUpdateController;
use App\Http\Controllers\Summary\DepartmentResultSummaryController;
use App\Http\Controllers\Vetting\VettingController;
use App\Http\Middleware\ValidateMonthParameter;
use App\Http\Middleware\ValidateYearParameter;
use App\Models\ExcelImportEvent;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(static function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::prefix('profile')->group(static function (): void {
        Route::get('', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('results')->group(static function (): void {
        Route::get('{student?}', [ViewStudentResultController::class, 'index'])->name('results.index');
        Route::post('', [ViewStudentResultController::class, 'store'])->name('results.store');
        Route::get('{student}/print', [ViewStudentResultController::class, 'print'])
            ->name('results.print');
    });

    Route::prefix('final-results/student/')->group(static function (): void {
        Route::get('{student?}', [StudentFinalResultController::class, 'index'])->name('finalResults.index');
        Route::post('', [StudentFinalResultController::class, 'store'])->name('finalResults.store');
        Route::get('{student}/print', [StudentFinalResultController::class, 'print'])
            ->name('finalResults.print');
        Route::get('{student}/transcript', [StudentFinalResultController::class, 'transcript'])
            ->name('finalResults.transcript');
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
        Route::post('department-session', DownloadRegistrationsByDepartmentSessionController::class)
            ->name('download.registrations.department-session.store');
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
        Route::post('department-session', DownloadResultsByDepartmentSessionController::class)
            ->name('download.results.department-session.store');
        Route::post('department-session-level', DownloadResultByDepartmentSessionLevelController::class)
            ->name('download.results.department-session-level.store');
        Route::post('department-session-semester', DownloadResultByDepartmentSessionSemesterController::class)
            ->name('download.results.department-session-semester.store');
        Route::post('session-course', DownloadResultBySessionCourseController::class)
            ->name('download.results.session-course.store');
    });

    Route::prefix('vetting')->group(static function (): void {
        Route::get('{department?}', [VettingController::class, 'index'])->name('vetting.index');
        Route::post('', [VettingController::class, 'store'])->name('vetting.store');
        Route::get('create/{student}', [VettingController::class, 'create'])->name('vetting.create');
    });

    Route::prefix('department/cleared/students')->group(static function (): void {
        Route::get('{department?}/{year?}/{month?}', [DepartmentClearedController::class, 'index'])
            ->name('department.cleared.index')
            ->middleware([ValidateYearParameter::class, ValidateMonthParameter::class]);
        Route::post('', [DepartmentClearedController::class, 'store'])->name('department.cleared.store');
    });

    Route::get('student/{student?}', [StudentController::class, 'show'])->name('students.show');

    Route::prefix('students')->group(static function (): void {
        Route::get('', [StudentController::class, 'index'])->name('students.index');
        Route::post('', [StudentController::class, 'store'])->name('students.store');
        Route::post('/{student}/clearance', [ClearanceController::class, 'store'])->name('students.clearance.store');

        Route::prefix('{student}/update')->group(static function (): void {
            Route::patch('registration-number', RegistrationNumberController::class)
                ->name('student.registrationNumber.update');
            Route::patch('name', StudentNameController::class)->name('student.name.update');
            Route::patch('status', StudentStatusUpdateController::class)->name('student.status.update');
            Route::patch('birth-date', BirthDateUpdateController::class)->name('student.birthDate.update');
            Route::patch('entry-level', EntryLevelUpdateController::class)->name('student.entryLevel.update');
            Route::patch('entry-mode', EntryModeUpdateController::class)->name('student.entryMode.update');
            Route::patch('entry-session', EntrySessionUpdateController::class)
                ->name('student.entrySession.update');
            Route::patch('gender', GenderUpdateController::class)->name('student.gender.update');
            Route::patch('jamb-registration-number', JambRegistrationNumberUpdateController::class)
                ->name('student.jambRegistrationNumber.update');
            Route::patch('local-government', LocalGovernmentUpdateController::class)
                ->name('student.localGovernment.update');
            Route::patch('program', ProgramUpdateController::class)->name('student.program.update');
            Route::patch('email', StudentEmailUpdateController::class)->name('student.email.update');
            Route::patch('phone-number', StudentPhoneNumberUpdateController::class)
                ->name('student.phoneNumber.update');
            Route::patch('result', ResultUpdateController::class)->name('student.result.update');
        });
    });

    Route::prefix('export')->group(static function (): void {
        Route::prefix('results')->group(static function (): void {
            Route::get('page', ExportResultsPageController::class)->name('export.results.page');

            Route::post('registration-number', [ExportResultsByRegistrationNumberController::class, 'store'])
                ->name('export.results.registration-number.store');
            Route::get('registration-number', [ExportResultsByRegistrationNumberController::class, 'download'])
                ->name('export.results.registration-number.download');

            Route::post('registration-numbers', [RegistrationNumberListResultsExportController::class, 'store'])
                ->name('export.results.registration-numbers.store');
            Route::get('registration-numbers', [RegistrationNumberListResultsExportController::class, 'download'])
                ->name('export.results.registration-numbers.download');

            Route::post('department-session', [ExportResultsByDepartmentSessionController::class, 'store'])
                ->name('export.results.department-session.store');
            Route::get('department/{department}/session/{session}',
                [ExportResultsByDepartmentSessionController::class, 'download'])
                ->name('export.results.department-session.download');
        });
    });

    Route::prefix('import')->group(static function (): void {
        Route::prefix('final-results')->group(static function (): void {
            Route::get('', [FinalResultImportController::class, 'index'])->name('import.final-results.index');
            Route::post('', [FinalResultImportController::class, 'store'])->name('import.final-results.store');
            Route::post('delete/{event}', [FinalResultImportController::class, 'delete'])
                ->can('delete', ExcelImportEvent::class)
                ->name('import.final-results.delete');
        });

        Route::prefix('curriculum')->group(static function (): void {
            Route::get('', [ProgramCurriculumImportController::class, 'index'])
                ->name('import.curriculum.index');
            Route::post('', [ProgramCurriculumImportController::class, 'store'])
                ->name('import.curriculum.store');
            Route::post('delete/{event}', [ProgramCurriculumImportController::class, 'delete'])
                ->can('delete', ExcelImportEvent::class)
                ->name('import.curriculum.delete');
        });
    });
});

require __DIR__ . '/auth.php';
