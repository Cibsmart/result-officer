<?php

declare(strict_types=1);

use App\Data\Cleared\StudentData;
use App\Data\Cleared\StudentListData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Factories\ProgramFactory;
use Tests\Factories\StatusChangeEventFactory;
use Tests\Factories\StudentFactory;

covers(StudentListData::class);

it('creates department cleared student list', function (): void {
    $program = ProgramFactory::new()->createOne();
    $count = 3;

    StudentFactory::new()
        ->for($program)
        ->cleared()
        ->has(StatusChangeEventFactory::new()->cleared())
        ->createMany($count);

    $data = StudentListData::fromModel($program->department, Carbon::now()->year);

    expect($data)->toBeInstanceOf(StudentListData::class)
        ->data->toHaveCount($count)->toBeInstanceOf(Collection::class)
        ->data->each->toBeInstanceOf(StudentData::class);
});
