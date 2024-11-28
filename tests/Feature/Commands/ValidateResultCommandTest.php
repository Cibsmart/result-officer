<?php

declare(strict_types=1);

use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;

use function Pest\Laravel\artisan;

it('validates results pending validation', closure: function (): void {
    RegistrationFactory::new()->has(ResultFactory::new())->createOne();

    artisan('rp:results-validate')->assertExitCode(0);
});
