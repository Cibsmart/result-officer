<?php

declare(strict_types=1);

use App\Enums\ComputationStrategy;
use App\Models\FinalStudent;
use Tests\Factories\FinalSessionEnrollmentFactory;
use Tests\Factories\FinalStudentFactory;
use Tests\Factories\InstitutionFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(FinalStudent::class);

it('computes student result count ', function (): void {
    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->state(['result_count' => 10]))
        ->createOne();

    $count = $student->getResultCount();

    expect($count)->toBe(20);
});

it('computes student credit unit sum', function (): void {
    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->state(['credit_unit_sum' => 24]))
        ->createOne();

    $cus = $student->getCreditUnitSum();

    expect($cus)->toBe(48);
});

it('computes student grade point sum', function (): void {
    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->state(['grade_point_sum' => 45]))
        ->createOne();

    $gps = $student->getGradePointSum();

    expect($gps)->toBe(90);
});

it('computes sum of student cumulative grade point averages', function (): void {
    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->sequence(['cumulative_grade_point_average' => 3.111], ['cumulative_grade_point_average' => 2.712]))
        ->createOne();

    $gpas = $student->getCumulativeGradePointAverageSum();

    expect($gpas)->toBe(5.823);
});

it('computes student fcgpa using semester strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->sequence(['cumulative_grade_point_average' => 3.111], ['cumulative_grade_point_average' => 2.712]))
        ->createOne();

    $cgpa = $student->getFinalCumulativeGradePointAverage();

    expect($cgpa)->toBe(2.912);
});

it('computes student fcgpa using universal strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::UNIVERSAL->value]);

    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->state(['credit_unit_sum' => 26, 'grade_point_sum' => 90]))
        ->createOne();

    $cgpa = $student->getFinalCumulativeGradePointAverage();

    expect($cgpa)->toBe(3.462);
});

it('updates student credit unit, grade point sum and averages', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $student = FinalStudentFactory::new()
        ->has(FinalSessionEnrollmentFactory::new()->count(2)
            ->sequence(['cumulative_grade_point_average' => 3.111], ['cumulative_grade_point_average' => 2.712]))
        ->createOne();

    $count = $student->getResultCount();
    $cus = $student->getCreditUnitSum();
    $gps = $student->getGradePointSum();
    $cgpas = $student->getCumulativeGradePointAverageSum();
    $fcgpa = $student->getFinalCumulativeGradePointAverage();

    $student->updateCountSumAndAverages();

    assertDatabaseHas(FinalStudent::class, [
        'credit_unit_sum' => $cus,
        'cumulative_grade_point_average_sum' => $cgpas * 1_000,
        'final_cumulative_grade_point_average' => $fcgpa * 1_000,
        'grade_point_sum' => $gps,
        'id' => $student->id,
        'result_count' => $count,
    ]);
});
