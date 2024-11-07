<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\RegistrationClient;
use App\Contracts\ResultClient;
use App\Contracts\StudentClient;
use App\Http\Clients\PortalRegistrationClient;
use App\Http\Clients\PortalResultClient;
use App\Http\Clients\PortalStudentClient;
use App\Models\ProgramCurriculumCourse;
use App\Models\Registration;
use App\Models\Result;
use App\Models\SemesterEnrollment;
use App\Models\SessionEnrollment;
use App\Models\Student;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(App::isLocal());

        Relation::enforceMorphMap([
            'programCurriculumCourse' => ProgramCurriculumCourse::class,
            'registration' => Registration::class,
            'result' => Result::class,
            'semesterEnrollment' => SemesterEnrollment::class,
            'sessionEnrollment' => SessionEnrollment::class,
            'student' => Student::class,
        ]);
    }
}
