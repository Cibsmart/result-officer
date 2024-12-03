<?php

declare(strict_types=1);

use App\Data\Cleared\StudentData;
use App\Enums\StudentStatus;
use Tests\Factories\StatusChangeEventFactory;
use Tests\Factories\StudentFactory;

covers(StudentData::class);

it('creates cleared student data from student model', function (): void {
    $student = StudentFactory::new()->cleared()
        ->has(StatusChangeEventFactory::new()->cleared())
        ->createOne();

    $data = StudentData::fromModel($student);

    expect($data)->toBeInstanceOf(StudentData::class)
        ->toHaveProperties(['id', 'name', 'registrationNumber', 'gender', 'status', 'dateCleared'])
        ->id->toBe($student->id)
        ->name->toBe($student->name)
        ->gender->toBe($student->gender)
        ->registrationNumber->toBe($student->registration_number)
        ->status->toBe(StudentStatus::CLEARED)
        ->fcgpa->toBe(number_format($student->fcgpa, 2))
        ->dateCleared->toBe($student->statusChangeEvents->first()->date->format('Y-m-d H:i:s'));
});
