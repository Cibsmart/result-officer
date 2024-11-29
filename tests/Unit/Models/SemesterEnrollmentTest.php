<?php

declare(strict_types=1);

use App\Models\SemesterEnrollment;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(SemesterEnrollment::class);

it('computes the number of courses', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5),
        )->createOne();

    $count = $enrollment->courseCount();

    expect($count)->toBe(5);
});

it('computes credit unit sum', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5),
        )->createOne();

    $cus = $enrollment->creditUnitSum();

    expect($cus)->toBe(15);
});

it('computes grade point sum', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5)
            ->has(ResultFactory::new()->state(['grade' => 'C'])),
        )->createOne();

    $gps = $enrollment->gradePointSum();

    expect($gps)->toBe(45);
});

it('computes grade point average', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5)
            ->has(ResultFactory::new()->state(['grade' => 'C'])),
        )->createOne();

    $gpa = $enrollment->gradePointAverage();

    expect($gpa)->toBe(3.000);
});

it('returns zero grade point for registrations without result', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->state(['credit_unit' => 3]),
        )->createOne();

    $gps = $enrollment->gradePointSum();

    expect($gps)->toBe(0);
});

it('updates the credit unit, grade point sum and average columns', function (): void {
    $enrollment = SemesterEnrollmentFactory::new()
        ->has(RegistrationFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5)
            ->has(ResultFactory::new()->state(['grade' => 'C'])),
        )->createOne();

    $count = $enrollment->courseCount();
    $cus = $enrollment->creditUnitSum();
    $gps = $enrollment->gradePointSum();
    $gpa = $enrollment->gradePointAverage() * 1000;

    $enrollment->updateSumsAndAverage();

    assertDatabaseHas(SemesterEnrollment::class, [
        'course_count' => $count,
        'cus' => $cus,
        'gpa' => $gpa,
        'gps' => $gps,
        'id' => $enrollment->id,
    ]);
});
