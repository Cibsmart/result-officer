<?php

declare(strict_types=1);

use App\Data\Cleared\ClearedStudentData;
use App\Data\Cleared\ClearedStudentListData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Factories\FinalStudentFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\StatusChangeEventFactory;
use Tests\Factories\StudentFactory;

covers(ClearedStudentListData::class);

it('creates department cleared student list', function (): void {
    $program = ProgramFactory::new()->createOne();
    $count = 3;
    $year = now()->year;
    $month = now()->monthName;

    StudentFactory::new()
        ->for($program)
        ->cleared()
        ->has(StatusChangeEventFactory::new()->cleared())
        ->has(FinalStudentFactory::new()->state(['month' => $month, 'year' => $year]))
        ->createMany($count);

    $data = ClearedStudentListData::fromModel($program->department, $year, $month);

    expect($data)->toBeInstanceOf(ClearedStudentListData::class)
        ->data->toHaveCount($count)->toBeInstanceOf(Collection::class)
        ->data->each->toBeInstanceOf(ClearedStudentData::class);
});
