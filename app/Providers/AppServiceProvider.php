<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CourseRegistrationClient;
use App\Contracts\PortalDataService;
use App\Contracts\ResultClient;
use App\Contracts\StudentClient;
use App\Enums\ImportEventType;
use App\Http\Clients\PortalCourseRegistrationClient;
use App\Http\Clients\PortalResultClient;
use App\Http\Clients\PortalStudentClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Context;
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
        $this->app->bind(CourseRegistrationClient::class, PortalCourseRegistrationClient::class);

        $this->app->bind(
            PortalDataService::class,
            fn($app) => $app->make(ImportEventType::from(Context::pull('import-event'))->service()),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(App::isLocal());
    }
}
