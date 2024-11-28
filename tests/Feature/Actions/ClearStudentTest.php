<?php

declare(strict_types=1);

use App\Actions\Clearing\ClearStudent;
use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use App\Models\StatusChangeEvent;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ClearStudent::class);

it('clears a student', function (): void {
    $student = StudentFactory::new()->createOne(['status' => StudentStatus::FINAL_YEAR]);
    VettingEventFactory::new()->for($student)->createOne(['status' => VettingEventStatus::PASSED]);

    $action = new ClearStudent();

    $action->execute($student);

    expect($student->status)->toBe(StudentStatus::CLEARED);
});

it('records status change event', function (): void {
    $student = StudentFactory::new()->createOne(['status' => StudentStatus::FINAL_YEAR]);
    VettingEventFactory::new()->for($student)->createOne(['status' => VettingEventStatus::PASSED]);

    $action = new ClearStudent();

    $action->execute($student);

    assertDatabaseHas(StatusChangeEvent::class, [
        'status' => StudentStatus::CLEARED,
        'student_id' => $student->id,
    ]);
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
    [StudentStatus::CLEARED],
    [StudentStatus::GRADUATED],
])->throws(Exception::class);
