<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\DeleteStudent;
use App\Contracts\RegistrationClient;
use App\Contracts\ResultClient;
use App\Contracts\StudentClient;
use App\Enums\VettingType;
use App\Http\Clients\PortalRegistrationClient;
use App\Http\Clients\PortalResultClient;
use App\Http\Clients\PortalStudentClient;
use App\Models\Program;
use App\Models\ProgramCurriculumCourse;
use App\Models\ProgramCurriculumElectiveGroup;
use App\Models\ProgramCurriculumSemester;
use App\Models\Registration;
use App\Models\Result;
use App\Models\SemesterEnrollment;
use App\Models\Student;
use App\Models\User;
use App\Services\Vetting\Steps\CheckCoreCoursesStep;
use App\Services\Vetting\Steps\CheckCreditUnitsStep;
use App\Services\Vetting\Steps\CheckElectiveCoursesStep;
use App\Services\Vetting\Steps\CheckFailedCoursesStep;
use App\Services\Vetting\Steps\CheckFirstYearCoursesStep;
use App\Services\Vetting\Steps\CheckResultsValidityStep;
use App\Services\Vetting\Steps\CheckSemesterCreditLimitsStep;
use App\Services\Vetting\Steps\CheckStudyYearStep;
use App\Services\Vetting\Steps\MatchCurriculumCoursesStep;
use App\Services\Vetting\Steps\MatchCurriculumSemestersStep;
use App\Services\Vetting\Vetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as TelescopeServiceProviderAlias;

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

        if ($this->app->environment('local') && class_exists(TelescopeServiceProviderAlias::class)) {
            $this->app->register(TelescopeServiceProviderAlias::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(Vetting::class, function ($app) {
            // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys
            $steps = [
                VettingType::ORGANIZE_STUDY_YEAR->value => $app->make(CheckStudyYearStep::class),
                VettingType::VALIDATE_RESULTS->value => $app->make(CheckResultsValidityStep::class),
                VettingType::MATCH_SEMESTERS->value => $app->make(MatchCurriculumSemestersStep::class),
                VettingType::CHECK_SEMESTER_CREDIT_LOADS->value => $app->make(CheckSemesterCreditLimitsStep::class),
                VettingType::MATCH_COURSES->value => $app->make(MatchCurriculumCoursesStep::class),
                VettingType::CHECK_CREDIT_UNITS->value => $app->make(CheckCreditUnitsStep::class),
                VettingType::CHECK_FIRST_YEAR_COURSES->value => $app->make(CheckFirstYearCoursesStep::class),
                VettingType::CHECK_CORE_COURSES->value => $app->make(CheckCoreCoursesStep::class),
                VettingType::CHECK_ELECTIVE_COURSES->value => $app->make(CheckElectiveCoursesStep::class),
                VettingType::CHECK_FAILED_COURSES->value => $app->make(CheckFailedCoursesStep::class),
            ];

            return new Vetting($steps);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(fn (User $user) => $user->isSuperAdmin() ? true : null);

        Model::unguard();

        Model::shouldBeStrict(App::isLocal());

        Model::automaticallyEagerLoadRelationships();

        DeleteStudent::prohibit(App::isProduction());

        DB::prohibitDestructiveCommands(App::isProduction());

        Relation::enforceMorphMap([
            'program' => Program::class,
            'programCurriculumCourse' => ProgramCurriculumCourse::class,
            'programCurriculumElectiveGroup' => ProgramCurriculumElectiveGroup::class,
            'programCurriculumSemester' => ProgramCurriculumSemester::class,
            'registration' => Registration::class,
            'result' => Result::class,
            'semesterEnrollment' => SemesterEnrollment::class,
            'student' => Student::class,
            'user' => User::class,
        ]);
    }
}
