<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\RegistrationClient;
use App\Contracts\ResultClient;
use App\Contracts\StudentClient;
use App\Enums\VettingType;
use App\Http\Clients\PortalRegistrationClient;
use App\Http\Clients\PortalResultClient;
use App\Http\Clients\PortalStudentClient;
use App\Models\Program;
use App\Models\ProgramCurriculumCourse;
use App\Models\ProgramCurriculumSemester;
use App\Models\Registration;
use App\Models\Result;
use App\Models\SemesterEnrollment;
use App\Models\SessionEnrollment;
use App\Models\Student;
use App\Services\Vetting\Steps\CheckResultsValidityStep;
use App\Services\Vetting\Steps\CheckSemesterCreditLimitsStep;
use App\Services\Vetting\Steps\MatchCurriculumCoursesStep;
use App\Services\Vetting\Steps\OrganizeStudyYearStep;
use App\Services\Vetting\Vetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ResultClient::class, PortalResultClient::class);
        $this->app->bind(StudentClient::class, PortalStudentClient::class);
        $this->app->bind(RegistrationClient::class, PortalRegistrationClient::class);

        $this->app->bind(Vetting::class, function ($app) {
            // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys
            $steps = [
                VettingType::ORGANIZE_STUDY_YEAR->value => $app->make(OrganizeStudyYearStep::class),
                VettingType::MATCH_COURSES->value => $app->make(MatchCurriculumCoursesStep::class),
                VettingType::VALIDATE_RESULTS->value => $app->make(CheckResultsValidityStep::class),
                VettingType::CHECK_SEMESTER_CREDIT_UNITS->value => $app->make(CheckSemesterCreditLimitsStep::class),
            ];

            return new Vetting($steps);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(App::isLocal());

        Relation::enforceMorphMap([
            'program' => Program::class,
            'programCurriculumCourse' => ProgramCurriculumCourse::class,
            'programCurriculumSemester' => ProgramCurriculumSemester::class,
            'registration' => Registration::class,
            'result' => Result::class,
            'semesterEnrollment' => SemesterEnrollment::class,
            'sessionEnrollment' => SessionEnrollment::class,
            'student' => Student::class,
        ]);
    }
}
