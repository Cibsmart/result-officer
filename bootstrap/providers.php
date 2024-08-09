<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\InertiaServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    InertiaServiceProvider::class,
];
