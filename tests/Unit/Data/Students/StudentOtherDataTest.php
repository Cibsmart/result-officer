<?php

declare(strict_types=1);

use App\Data\Students\StudentOtherData;
use Tests\Factories\StudentFactory;

it('returns correct other student data', function (): void {
    $student = StudentFactory::new()->createOne();

    $data = StudentOtherData::fromModel($student);

    expect($data)->toBeInstanceOf(StudentOtherData::class)
        ->toHaveProperties(['id', 'state', 'localGovernment', 'entryMode', 'entrySession', 'entryLevel']);
});
