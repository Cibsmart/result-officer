<?php

declare(strict_types=1);

use App\Enums\ComputationStrategy;
use App\Models\SessionEnrollment;
use Tests\Factories\InstitutionFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SessionEnrollmentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(SessionEnrollment::class);

it('computes course count for session enrollment', function (): void {
    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->state(['course_count' => 10]))
        ->createOne();

    $count = $enrollment->courseCount();

    expect($count)->toBe(20);
});

it('computes session credit unit sum', function (): void {
    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->state(['cus' => 15]))
        ->createOne();

    $cus = $enrollment->creditUnitSum();

    expect($cus)->toBe(30);
});

it('computes session grade point sum', function (): void {
    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->state(['gps' => 45]))
        ->createOne();

    $gps = $enrollment->gradePointSum();

    expect($gps)->toBe(90);
});

it('computes sum of the session grade point averages', function (): void {
    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->sequence(['gpa' => 3.111], ['gpa' => 2.712]))
        ->createOne();

    $gpas = $enrollment->gradePointAverageSum();

    expect($gpas)->toBe(5.823);
});

it('computes session cgpa using semester strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->sequence(['gpa' => 3.111], ['gpa' => 2.712]))
        ->createOne();

    $cgpa = $enrollment->cumulativeGradePointAverage();

    expect($cgpa)->toBe(2.912);
});

it('computes session cgpa using universal strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::UNIVERSAL->value]);

    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->state(['cus' => 26, 'gps' => 90]))
        ->createOne();

    $cgpa = $enrollment->cumulativeGradePointAverage();

    expect($cgpa)->toBe(3.462);
});

it('updates the credit unit, grade point sum and average columns', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $enrollment = SessionEnrollmentFactory::new()
        ->has(SemesterEnrollmentFactory::new()->count(2)
            ->sequence(['gpa' => 3.111], ['gpa' => 2.712]))
        ->createOne();

    $count = $enrollment->courseCount();
    $cus = $enrollment->creditUnitSum();
    $gps = $enrollment->gradePointSum();
    $gpas = $enrollment->gradePointAverageSum();
    $cgpa = $enrollment->cumulativeGradePointAverage();

    $enrollment->updateCountSumAndAverages();

    assertDatabaseHas(SessionEnrollment::class, [
        'cgpa' => $cgpa * 1000,
        'course_count' => $count,
        'cus' => $cus,
        'gpas' => $gpas * 1000,
        'gps' => $gps,
        'id' => $enrollment->id,
    ]);
});
