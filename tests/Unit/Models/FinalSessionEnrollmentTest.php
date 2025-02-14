<?php

declare(strict_types=1);

use App\Enums\ComputationStrategy;
use App\Models\FinalSessionEnrollment;
use Tests\Factories\FinalSemesterEnrollmentFactory;
use Tests\Factories\FinalSessionEnrollmentFactory;
use Tests\Factories\InstitutionFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(FinalSessionEnrollment::class);

it('computes course count for session enrollment', function (): void {
    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->state(['result_count' => 10]))
        ->createOne();

    $count = $enrollment->getResultCount();

    expect($count)->toBe(20);
});

it('computes session credit unit sum', function (): void {
    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->state(['credit_unit_sum' => 15]))
        ->createOne();

    $cus = $enrollment->getCreditUnitSum();

    expect($cus)->toBe(30);
});

it('computes session grade point sum', function (): void {
    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->state(['grade_point_sum' => 45]))
        ->createOne();

    $gps = $enrollment->getGradePointSum();

    expect($gps)->toBe(90);
});

it('computes sum of the session grade point averages', function (): void {
    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->sequence(['grade_point_average' => 3.111], ['grade_point_average' => 2.712]))
        ->createOne();

    $gpas = $enrollment->getGradePointAverageSum();

    expect($gpas)->toBe(5.823);
});

it('computes session cgpa using semester strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->sequence(['grade_point_average' => 3.111], ['grade_point_average' => 2.712]))
        ->createOne();

    $cgpa = $enrollment->getCumulativeGradePointAverage();

    expect($cgpa)->toBe(2.912);
});

it('computes session cgpa using universal strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::UNIVERSAL->value]);

    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->state(['credit_unit_sum' => 26, 'grade_point_sum' => 90]))
        ->createOne();

    $cgpa = $enrollment->getCumulativeGradePointAverage();

    expect($cgpa)->toBe(3.462);
});

it('updates the credit unit, grade point sum and average columns', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $enrollment = FinalSessionEnrollmentFactory::new()
        ->has(FinalSemesterEnrollmentFactory::new()->count(2)
            ->sequence(['grade_point_average' => 3.111], ['grade_point_average' => 2.712]))
        ->createOne();

    $count = $enrollment->getResultCount();
    $cus = $enrollment->getCreditUnitSum();
    $gps = $enrollment->getGradePointSum();
    $gpas = $enrollment->getGradePointAverageSum();
    $cgpa = $enrollment->getCumulativeGradePointAverage();

    $enrollment->updateCountSumAndAverages();

    assertDatabaseHas(FinalSessionEnrollment::class, [
        'credit_unit_sum' => $cus,
        'cumulative_grade_point_average' => $cgpa * 1_000,
        'grade_point_average_sum' => $gpas * 1_000,
        'grade_point_sum' => $gps,
        'id' => $enrollment->id,
        'result_count' => $count,
    ]);
});
