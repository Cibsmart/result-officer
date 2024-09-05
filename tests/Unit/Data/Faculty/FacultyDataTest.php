<?php

declare(strict_types=1);

use App\Data\Faculty\FacultyData;
use Tests\Factories\FacultyFactory;
use Tests\Factories\ProgramFactory;

test('faculty dto is correct', function (): void {
    $faculty = FacultyFactory::new()->createOne();

    $data = FacultyData::fromModel($faculty);

    expect($data)->toBeInstanceOf(FacultyData::class)
        ->and($data->id)->toBe($faculty->id)
        ->and($data->name)->toBe($faculty->name);
});

it('can create faculty dto from program', function (): void {
    $program = ProgramFactory::new()->createOne();

    $data = FacultyData::fromProgram($program);

    expect($data)->toBeInstanceOf(FacultyData::class)
        ->and($data->id)->toBe($program->department->faculty->id)
        ->and($data->name)->toBe($program->department->faculty->name);
});
