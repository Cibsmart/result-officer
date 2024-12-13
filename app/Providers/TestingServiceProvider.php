<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Spatie\LaravelData\Data;

final class TestingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        $this->assertableInertiaMacros();

        $this->testResponseMacros();
    }

    private function assertableInertiaMacros(): void
    {
        AssertableInertia::macro('hasData', function (string $key, Data $data) {

            $this->has($key);

            expect($this->prop($key))->toEqual($data->toArray());

            return $this;
        });

        AssertableInertia::macro('hasDataList', function (string $key, Data $data) {
            $this->hasData("{$key}", $data);

            expect($this->prop($key))->toHaveKey('data');

            return $this;
        });

        AssertableInertia::macro('hasPaginatedData', function (string $key, Paginator $data) {

            expect($this->prop($key))->toHaveKeys(['links', 'data'])
                ->and($this->prop($key))->toEqual($data->toArray());

            return $this;
        });
    }

    private function testResponseMacros(): void
    {
        TestResponse::macro(
            'assertHasData',
            fn (string $key, Data $data) => $this->assertInertia(fn (AssertableInertia $inertia) => $inertia
                ->hasData($key, $data)),
        );

        TestResponse::macro(
            'assertHasDataList',
            fn (string $key, Data $data) => $this->assertInertia(fn (AssertableInertia $inertia) => $inertia
                ->hasDataList($key, $data)),
        );

        TestResponse::macro(
            'assertHasPaginatedData',
            fn (string $key, Paginator $data) => $this->assertInertia(fn (AssertableInertia $inertia) => $inertia
                ->hasPaginatedData($key, $data)),
        );

        TestResponse::macro(
            'assertHasComponent',
            fn (string $path) => $this->assertInertia(fn (AssertableInertia $inertia) => $inertia->component($path,
                true)),
        );
    }
}
