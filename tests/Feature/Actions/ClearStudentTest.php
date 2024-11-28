<?php

declare(strict_types=1);

use App\Actions\Clearing\ClearStudent;
use App\Enums\StudentStatus;
use Tests\Factories\StudentFactory;

covers(ClearStudent::class);

it('clears a student', function (): void {
    $student = StudentFactory::new()->createOne(['status' => StudentStatus::VETTED]);

    $action = new ClearStudent();

    $action->execute($student);

    expect($student->status)->toBe(StudentStatus::CLEARED);
});

it('throws exception for a student not in clearable state', function (StudentStatus $status): void {
    $student = StudentFactory::new()->createOne(['status' => $status]);

    $action = new ClearStudent();

    $action->execute($student);

})->with([
    [StudentStatus::NEW],
    [StudentStatus::ACTIVE],
    [StudentStatus::INACTIVE],
    [StudentStatus::PROBATION],
    [StudentStatus::WITHDRAWN],
    [StudentStatus::EXPELLED],
    [StudentStatus::SUSPENDED],
    [StudentStatus::DECEASED],
    [StudentStatus::TRANSFERRED],
    [StudentStatus::FINAL_YEAR],
    [StudentStatus::EXTRA_YEAR],
    [StudentStatus::VETTING],
    [StudentStatus::CLEARED],
    [StudentStatus::GRADUATED],
])->throws(Exception::class);
