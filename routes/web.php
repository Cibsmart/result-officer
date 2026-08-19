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
use App\Http\Controllers\Imports\ResultImportController;
use App\Http\Controllers\Registrations\RegistrationController;
use App\Http\Controllers\Reports\CompositeSheetController;
use App\Http\Controllers\Reports\DepartmentClearedController;
use App\Http\Controllers\Results\ViewStudentResultController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\Search\SearchSuggestionController;
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
use App\Http\Controllers\Vetting\GraduandController;
use App\Http\Controllers\Vetting\VettingController;
use App\Http\Controllers\Vetting\VettingEventController;
use App\Http\Middleware\ValidateMonthParameter;
use App\Http\Middleware\ValidateYearParameter;
use App\Models\Registration;
use App\Models\Student;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(static function (): void {
    Route::get('/', DashboardController::class)->name('dashboard')
        ->middleware('permission:student.view');

    Route::prefix('search')->group(static function (): void {
        Route::get('', SearchController::class)->name('search')
            ->middleware('permission:student.view');
        Route::get('suggestions', SearchSuggestionController::class)->name('search.suggestions')
            ->middleware('permission:student.view');
    });

    Route::prefix('results')->group(static function (): void {
        Route::get('{student?}', [ViewStudentResultController::class, 'index'])->name('results.index')
            ->middleware('permission:result.view');
        Route::post('', [ViewStudentResultController::class, 'store'])->name('results.store')
            ->middleware('permission:result.view');
        Route::get('{student}/print', [ViewStudentResultController::class, 'print'])
            ->name('results.print')
            ->middleware('permission:result.view');
    });

    Route::prefix('final-results/student/')->group(static function (): void {
        Route::get('{student?}', [StudentFinalResultController::class, 'index'])->name('finalResults.index')
            ->middleware('permission:graduand.view');
        Route::post('', [StudentFinalResultController::class, 'store'])->name('finalResults.store')
            ->middleware('permission:graduand.view');
        Route::get('{student}/print', [StudentFinalResultController::class, 'print'])->name('finalResults.print')
            ->middleware('permission:graduand.view');
        Route::get('{student}/transcript', [StudentFinalResultController::class, 'transcript'])
            ->name('finalResults.transcript')
            ->middleware('permission:transcript.view');
        Route::get('{student}/download', [StudentFinalResultController::class, 'download'])
            ->name('finalResults.download')
            ->middleware('permission:graduand.view');
    });

    Route::prefix('summary')->group(static function (): void {
        Route::get('', [DepartmentResultSummaryController::class, 'form'])->name('summary.form')
            ->middleware('permission:result.view');
        Route::post('', [DepartmentResultSummaryController::class, 'view'])->name('summary.view')
            ->middleware('permission:result.view');
        Route::get('{department}/{session}/{level}', [DepartmentResultSummaryController::class, 'print'])
            ->name('summary.print')
            ->middleware('permission:result.view');
    });

    Route::prefix('composite')->group(static function (): void {
        Route::get('', [CompositeSheetController::class, 'form'])->name('composite.form')
            ->middleware('permission:result.view');
        Route::post('', [CompositeSheetController::class, 'view'])->name('composite.view')
            ->middleware('permission:result.view');
        Route::get('{program}/{session}/{level}/{semester}', [CompositeSheetController::class, 'print'])
            ->name('composite.print')
            ->middleware('permission:result.view');
    });

    Route::prefix('download/students')->group(static function (): void {
        Route::get('page', DownloadStudentsPageController::class)->name('download.students.page')
            ->middleware('permission:import.portal-download');
        Route::post('registration-number', DownloadStudentByRegistrationNumberController::class)
            ->name('download.student.registration-number.store')
            ->middleware('permission:import.portal-download');
        Route::post('department-session', DownloadStudentsByDepartmentSessionController::class)
            ->name('download.students.department-session.store')
            ->middleware('permission:import.portal-download');
        Route::post('session', DownloadStudentsBySessionController::class)
            ->name('download.students.session.store')
            ->middleware('permission:import.portal-download');
    });

    Route::prefix('download/departments')->group(static function (): void {
        Route::get('page', DownloadDepartmentsPageController::class)->name('download.departments.page')
            ->middleware('permission:import.portal-download');
        Route::post('', DownloadDepartmentsController::class)->name('download.departments.store')
            ->middleware('permission:import.portal-download');
    });

    Route::prefix('download/courses')->group(static function (): void {
        Route::get('', DownloadCoursesPageController::class)
            ->name('download.courses.page')
            ->middleware('permission:import.portal-download');
        Route::post('', DownloadCoursesController::class)
            ->name('download.courses.store')
            ->middleware('permission:import.portal-download');
    });

    Route::prefix('import-event')->group(static function (): void {
        Route::get('cancel/{event}', CancelImportEventController::class)
            ->name('import.event.cancel')
            ->middleware('permission:import.manage-event');
        Route::get('continue/{event}', ContinueImportEventController::class)
            ->name('import.event.continue')
            ->middleware('permission:import.manage-event');
    });

    Route::prefix('download/registrations')->group(static function (): void {
        Route::get('page', DownloadRegistrationPageController::class)
            ->name('download.registrations.page')
            ->middleware('permission:import.portal-download');
        Route::post('department-session', DownloadRegistrationsByDepartmentSessionController::class)
            ->name('download.registrations.department-session.store')
            ->middleware('permission:import.portal-download');
        Route::post('registration-number', DownloadRegistrationsByRegistrationNumberController::class)
            ->name('download.registrations.registration-number.store')
            ->middleware('permission:import.portal-download');
        Route::post('department-session-level', DownloadRegistrationsByDepartmentSessionLevelController::class)
            ->name('download.registrations.department-session-level.store')
            ->middleware('permission:import.portal-download');
        Route::post('department-session-semester', DownloadRegistrationsByDepartmentSessionSemesterController::class)
            ->name('download.registrations.department-session-semester.store')
            ->middleware('permission:import.portal-download');
        Route::post('session-course', DownloadRegistrationsBySessionCourseController::class)
            ->name('download.registrations.session-course.store')
            ->middleware('permission:import.portal-download');
    });

    Route::prefix('download/results')->group(static function (): void {
        Route::get('page', DownloadResultsPageController::class)
            ->name('download.results.page')
            ->middleware('permission:import.portal-download');
        Route::post('registration-number', DownloadResultByRegistrationNumberController::class)
            ->name('download.results.registration-number.store')
            ->middleware('permission:import.portal-download');
        Route::post('department-session', DownloadResultsByDepartmentSessionController::class)
            ->name('download.results.department-session.store')
            ->middleware('permission:import.portal-download');
        Route::post('department-session-level', DownloadResultByDepartmentSessionLevelController::class)
            ->name('download.results.department-session-level.store')
            ->middleware('permission:import.portal-download');
        Route::post('department-session-semester', DownloadResultByDepartmentSessionSemesterController::class)
            ->name('download.results.department-session-semester.store')
            ->middleware('permission:import.portal-download');
        Route::post('session-course', DownloadResultBySessionCourseController::class)
            ->name('download.results.session-course.store')
            ->middleware('permission:import.portal-download');
    });

    Route::prefix('graduands')->group(static function (): void {
        Route::get('{department?}', [GraduandController::class, 'index'])
            ->name('graduand.index')
            ->middleware('permission:graduand.view');
        Route::post('', [GraduandController::class, 'store'])->name('graduand.store')
            ->middleware('permission:graduand.view');
    });

    Route::prefix('vetting')->group(static function (): void {
        Route::get('create/{student}', [VettingController::class, 'create'])
            ->name('vetting.create')
            ->middleware('permission:vetting.run');
    });

    Route::prefix('vetting-event')->group(static function (): void {
        Route::get('', [VettingEventController::class, 'index'])->name('vettingEvent.index')
            ->middleware('permission:vetting.view');
        Route::post('', [VettingEventController::class, 'store'])->name('vettingEvent.store')
            ->middleware('permission:vetting.run');
        Route::get('show/{vettingEvent}', [VettingEventController::class, 'show'])->name('vettingEvent.show')
            ->middleware('permission:vetting.view');
        Route::delete('delete/{vettingEvent}', [VettingEventController::class, 'destroy'])
            ->name('vettingEvent.destroy')
            ->middleware('permission:vetting.run');
    });

    Route::prefix('department/cleared/students')->group(static function (): void {
        Route::get('{department?}/{year?}/{month?}', [DepartmentClearedController::class, 'index'])
            ->name('department.cleared.index')
            ->middleware('permission:graduand.view')
            ->middleware([ValidateYearParameter::class, ValidateMonthParameter::class]);
        Route::post('', [DepartmentClearedController::class, 'store'])->name('department.cleared.store')
            ->middleware('permission:graduand.view');
    });

    Route::get('student/{student?}', [StudentController::class, 'show'])->name('students.show')
        ->middleware('permission:student.view');

    Route::prefix('students')->group(static function (): void {
        Route::get('', [StudentController::class, 'index'])->name('students.index')
            ->middleware('permission:student.view');
        Route::post('', [StudentController::class, 'store'])->name('students.store')
            ->middleware('permission:student.view');
        Route::post('/{student}/clearance', [ClearanceController::class, 'store'])->name('students.clearance.store')
            ->middleware('permission:student.clear');

        Route::prefix('{student}/update')->group(static function (): void {
            Route::patch('registration-number', RegistrationNumberController::class)
                ->name('student.registrationNumber.update')
                ->middleware('permission:student.transfer');
            Route::patch('name', StudentNameController::class)->name('student.name.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('status', StudentStatusUpdateController::class)->name('student.status.update')
                ->middleware('permission:student.amend-placement');
            Route::patch('birth-date', BirthDateUpdateController::class)->name('student.birthDate.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('entry-level', EntryLevelUpdateController::class)->name('student.entryLevel.update')
                ->middleware('permission:student.amend-placement');
            Route::patch('entry-mode', EntryModeUpdateController::class)->name('student.entryMode.update')
                ->middleware('permission:student.amend-placement');
            Route::patch('entry-session', EntrySessionUpdateController::class)
                ->name('student.entrySession.update')
                ->middleware('permission:student.amend-placement');
            Route::patch('gender', GenderUpdateController::class)->name('student.gender.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('jamb-registration-number', JambRegistrationNumberUpdateController::class)
                ->name('student.jambRegistrationNumber.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('local-government', LocalGovernmentUpdateController::class)
                ->name('student.localGovernment.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('program', ProgramUpdateController::class)->name('student.program.update')
                ->middleware('permission:student.transfer');
            Route::patch('email', StudentEmailUpdateController::class)->name('student.email.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('phone-number', StudentPhoneNumberUpdateController::class)
                ->name('student.phoneNumber.update')
                ->middleware('permission:student.amend-demographics');
            Route::patch('result', ResultUpdateController::class)
                ->name('student.result.update')
                ->middleware('permission:result.amend')
                ->can('update', Registration::class);
        });

        Route::prefix('{student}/delete')->group(static function (): void {
            Route::delete('', [StudentController::class, 'destroy'])
                ->name('student.destroy')
                ->middleware('permission:student.delete')
                ->can('delete', Student::class);
            Route::delete('registration/{registration}', [RegistrationController::class, 'destroy'])
                ->name('student.registration.destroy')
                ->middleware('permission:registration.delete')
                ->can('delete', Registration::class);
        });
    });

    Route::prefix('export')->group(static function (): void {
        Route::prefix('results')->group(static function (): void {
            Route::get('page', ExportResultsPageController::class)->name('export.results.page')
                ->middleware('permission:result.export');

            Route::post('registration-number', [ExportResultsByRegistrationNumberController::class, 'store'])
                ->name('export.results.registration-number.store')
                ->middleware('permission:result.export');
            Route::get('registration-number', [ExportResultsByRegistrationNumberController::class, 'download'])
                ->name('export.results.registration-number.download')
                ->middleware('permission:result.export');

            Route::post('registration-numbers', [RegistrationNumberListResultsExportController::class, 'store'])
                ->name('export.results.registration-numbers.store')
                ->middleware('permission:result.export');
            Route::get('registration-numbers', [RegistrationNumberListResultsExportController::class, 'download'])
                ->name('export.results.registration-numbers.download')
                ->middleware('permission:result.export');

            Route::post('department-session', [ExportResultsByDepartmentSessionController::class, 'store'])
                ->name('export.results.department-session.store')
                ->middleware('permission:result.export');
            Route::get('department/{department}/session/{session}',
                [ExportResultsByDepartmentSessionController::class, 'download'])
                ->name('export.results.department-session.download')
                ->middleware('permission:result.export');
        });
    });

    Route::prefix('import')->group(static function (): void {
        Route::prefix('results')->group(static function (): void {
            Route::get('', [ResultImportController::class, 'index'])->name('import.results.index')
                ->middleware('permission:import.upload-spreadsheet');
            Route::post('', [ResultImportController::class, 'store'])->name('import.results.store')
                ->middleware('permission:import.upload-spreadsheet');
            Route::post('delete/{event}', [ResultImportController::class, 'delete'])
                ->can('delete', 'event')
                ->name('import.results.delete')
                ->middleware('permission:import.manage-event');
        });

        Route::prefix('final-results')->group(static function (): void {
            Route::get('', [FinalResultImportController::class, 'index'])->name('import.final-results.index')
                ->middleware('permission:import.upload-spreadsheet');
            Route::post('', [FinalResultImportController::class, 'store'])->name('import.final-results.store')
                ->middleware('permission:import.upload-spreadsheet');
            Route::post('delete/{event}', [FinalResultImportController::class, 'delete'])
                ->can('delete', 'event')
                ->name('import.final-results.delete')
                ->middleware('permission:import.manage-event');
        });

        Route::prefix('curriculum')->group(static function (): void {
            Route::get('', [ProgramCurriculumImportController::class, 'index'])->name('import.curriculum.index')
                ->middleware('permission:admin.curriculum');
            Route::post('', [ProgramCurriculumImportController::class, 'store'])->name('import.curriculum.store')
                ->middleware('permission:admin.curriculum');
            Route::post('delete/{event}', [ProgramCurriculumImportController::class, 'delete'])
                ->can('delete', 'event')->name('import.curriculum.delete')
                ->middleware('permission:import.manage-event');
        });
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
