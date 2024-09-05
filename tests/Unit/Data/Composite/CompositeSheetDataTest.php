<?php

declare(strict_types=1);

use App\Data\Composite\CompositeCourseListData;
use App\Data\Composite\CompositeRowData;
use App\Data\Composite\CompositeSheetData;
use App\Data\Faculty\FacultyData;
use App\Data\Level\LevelData;
use App\Data\Program\ProgramData;
use App\Data\Semester\SemesterData;
use App\Data\Session\SessionData;
use Illuminate\Support\Collection;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionFactory;

test('composite sheet data is correct', function (): void {
    $program = ProgramFactory::new()->createOne();
    $session = SessionFactory::new()->createOne();
    $semester = SemesterFactory::new()->createOne();
    $level = LevelFactory::new()->createOne();

    $data = CompositeSheetData::fromModel($program, $session, $level, $semester);

    expect($data)->toBeInstanceOf(CompositeSheetData::class)
        ->and($data->program)->toBeInstanceOf(ProgramData::class)
        ->and($data->faculty)->toBeInstanceOf(FacultyData::class)
        ->and($data->session)->toBeInstanceOf(SessionData::class)
        ->and($data->semester)->toBeInstanceOf(SemesterData::class)
        ->and($data->level)->toBeInstanceOf(LevelData::class)
        ->and($data->courses)->toBeInstanceOf(Collection::class)
        ->toContainOnlyInstancesOf(CompositeCourseListData::class)
        ->and($data->students)->toBeInstanceOf(Collection::class)
        ->toContainOnlyInstancesOf(CompositeRowData::class);
});
