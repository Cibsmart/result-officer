<?php

declare(strict_types=1);

use App\Models\FinalResult;
use App\Models\FinalSemesterEnrollment;
use Tests\Factories\FinalResultFactory;
use Tests\Factories\FinalSemesterEnrollmentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(FinalSemesterEnrollment::class);

it('computes the number of courses', function (): void {
    $enrollment = FinalSemesterEnrollmentFactory::new()
        ->has(FinalResultFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5),
        )->createOne();

    $count = $enrollment->getResultCount();

    expect($count)->toBe(5);
});

it('computes credit unit sum', function (): void {
    $enrollment = FinalSemesterEnrollmentFactory::new()
        ->has(FinalResultFactory::new()
            ->state(['credit_unit' => 3])
            ->count(5),
        )->createOne();

    $cus = $enrollment->getCreditUnitSum();

    expect($cus)->toBe(15);
});

it('computes grade point sum', function (): void {
    $enrollment = FinalSemesterEnrollmentFactory::new()
        ->has(FinalResultFactory::new()
            ->state(['credit_unit' => 3, 'grade' => 'C'])
            ->count(5),
        )->createOne();

    $gps = $enrollment->getGradePointSum();

    expect($gps)->toBe(45);
});

it('computes grade point average', function (): void {
    $enrollment = FinalSemesterEnrollmentFactory::new()
        ->has(FinalResultFactory::new()
            ->state(['credit_unit' => 3, 'grade' => 'C'])
            ->count(5),
        )->createOne();

    $gpa = $enrollment->getGradePointAverage();

    expect($gpa)->toBe(3.000);
});

it('updates the credit unit, grade point sum and average columns', function (): void {
    $enrollment = FinalSemesterEnrollmentFactory::new()
        ->has(FinalResultFactory::new()
            ->state(['credit_unit' => 3])
            ->sequence(['grade' => 'C'], ['grade' => 'D'])
            ->count(5),
        )->createOne();

    $count = $enrollment->getResultCount();
    $cus = $enrollment->getCreditUnitSum();
    $gps = $enrollment->getGradePointSum();
    $gpa = $enrollment->getGradePointAverage();

    $enrollment->updateSumsAndAverage();

    assertDatabaseHas(FinalSemesterEnrollment::class, [
        'result_count' => $count,
        'credit_unit_sum' => $cus,
        'grade_point_average' => $gpa * 1000,
        'grade_point_sum' => $gps,
        'id' => $enrollment->id,
    ]);
});
