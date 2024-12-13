<?php

declare(strict_types=1);

use App\Data\Students\StudentListPaginatedData;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Factories\StudentFactory;

it('returns paginated student list', function (): void {
    StudentFactory::new()->count(3)->create();

    $data = StudentListPaginatedData::new();

    expect($data)->toBeInstanceOf(StudentListPaginatedData::class)
        ->toHaveProperty('students')
        ->and($data->students)
        ->toBeInstanceOf(LengthAwarePaginator::class)
        ->toHaveProperty('items')
        ->toHaveCount(3);
});
