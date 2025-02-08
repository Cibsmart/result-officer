<?php

declare(strict_types=1);

use App\Data\Cleared\ClearedStudentData;
use App\Enums\StudentStatus;
use Tests\Factories\FinalStudentFactory;
use Tests\Factories\StatusChangeEventFactory;
use Tests\Factories\StudentFactory;

covers(ClearedStudentData::class);

it('creates cleared student data from student model', function (): void {
    $student = StudentFactory::new()->cleared()
        ->has(StatusChangeEventFactory::new()->cleared())
        ->createOne();

    $finalStudent = FinalStudentFactory::new()->for($student)->createOne();

    $data = ClearedStudentData::fromModel($student);

    expect($data)->toBeInstanceOf(ClearedStudentData::class)
        ->toHaveProperties(['id', 'name', 'registrationNumber', 'gender', 'status', 'batch'])
        ->id->toBe($student->id)
        ->name->toBe($student->name)
        ->gender->toBe($student->gender)
        ->registrationNumber->toBe($student->registration_number)
        ->status->toBe(StudentStatus::CLEARED)
        ->fcgpa->toBe(number_format($student->finalStudent->final_cummulative_grade_point_average, 2))
        ->batch->toBe("{$finalStudent->month} {$finalStudent->year}");
});
