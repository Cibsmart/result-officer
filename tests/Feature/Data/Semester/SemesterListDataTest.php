<?php

declare(strict_types=1);

use App\Data\Semester\SemesterListData;
use Tests\Factories\SemesterFactory;

test('semester list dto is correct', function (): void {
    $count = 2;

    SemesterFactory::new()->count($count)->create();

    $data = SemesterListData::new();

    expect($data)->toBeInstanceOf(SemesterListData::class)
        ->and($data->semesters->count())->toEqual($count + 1);
});
