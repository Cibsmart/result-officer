<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\InertiaServiceProvider;
use App\Providers\TestingServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    InertiaServiceProvider::class,
    TestingServiceProvider::class,
    HorizonServiceProvider::class,
];
