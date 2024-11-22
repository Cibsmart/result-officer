<?php

declare(strict_types=1);

use App\Data\Vetting\VettingStudentData;
use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;
use Tests\Factories\VettingReportFactory;
use Tests\Factories\VettingStepFactory;

covers(VettingStudentData::class);

it('returns valid vetting student data', function (): void {

    $student = StudentFactory::new()
        ->has(VettingEventFactory::new()
            ->has(VettingStepFactory::new()
                ->has(VettingReportFactory::new())))
        ->createOne();

    $data = VettingStudentData::fromModel($student);

    expect($data)->toBeInstanceOf(VettingStudentData::class)
        ->and($data->id)->toBe($student->id)
        ->and($data->name)->toBe($student->name)
        ->and($data->registrationNumber)->toBe($student->registration_number)
        ->and($data->studentStatus)->toBe(StudentStatus::NEW)
        ->and($data->vettingStatus)->toBe(VettingEventStatus::NEW)
        ->and($data->vettingStatusColor)->toBe(VettingEventStatus::NEW->color());
});

it('return valid vetting student data', function (): void {

    $student = StudentFactory::new()->createOne();

    $data = VettingStudentData::fromModel($student);

    expect($data)->toBeInstanceOf(VettingStudentData::class)
        ->and($data->vettingStatus)->toBe(VettingEventStatus::PENDING);
});
