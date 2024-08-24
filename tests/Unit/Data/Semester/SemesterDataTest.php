<?php

declare(strict_types=1);

use App\Data\Semester\SemesterData;
use Tests\Factories\SemesterFactory;

test('semester dto is correct', function (): void {
    $semester = SemesterFactory::new()->createOne();

    $data = SemesterData::from($semester);

    expect($data)->toBeInstanceOf(SemesterData::class)
        ->and($data->id)->toBe($semester->id)
        ->and($data->name)->toBe($semester->name);
});
