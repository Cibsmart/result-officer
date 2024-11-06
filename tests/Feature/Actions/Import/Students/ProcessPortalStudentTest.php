<?php

declare(strict_types=1);

use App\Actions\Import\Students\ProcessPortalStudent;
use App\Enums\RawDataStatus;
use App\Values\DateValue;
use Tests\Factories\DepartmentFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\LocalGovernmentFactory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\RawStudentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ProcessPortalStudent::class);

it('can process raw student and save into the students table', function (): void {
    $department = DepartmentFactory::new()->createOne(['online_id' => 1]);
    ProgramFactory::new()->createOne(['department_id' => $department->id, 'name' => $department->name]);
    LevelFactory::new()->createOne(['name' => 100]);
    SessionFactory::new()->createOne(['name' => '2009/2010']);
    LocalGovernmentFactory::new()->createOne(['name' => 'ABAKALIKI']);

    $rawStudent = RawStudentFactory::new()->createOne(['department_id' => (string) $department->id]);

    (new ProcessPortalStudent())->execute($rawStudent);

    expect($rawStudent->fresh()->status)->toBe(RawDataStatus::PROCESSED);

    assertDatabaseHas('students', [
        'date_of_birth' => DateValue::fromString($rawStudent->date_of_birth)->value,
        'first_name' => strtoupper($rawStudent->first_name),
        'gender' => $rawStudent->gender,
        'last_name' => strtoupper($rawStudent->last_name),
        'online_id' => $rawStudent->online_id,
        'other_names' => strtoupper($rawStudent->other_names),
        'registration_number' => $rawStudent->registration_number,
    ]);
});

it('does not save save duplicate student into the students table', function (): void {
    $student = StudentFactory::new()->createOne();
    $rawStudent = RawStudentFactory::new()->createOne(['registration_number' => $student->registration_number]);

    (new ProcessPortalStudent())->execute($rawStudent);

    expect($rawStudent->fresh()->status)->toBe(RawDataStatus::DUPLICATE);
});
