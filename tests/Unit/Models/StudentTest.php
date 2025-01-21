<?php

declare(strict_types=1);

use App\Enums\ComputationStrategy;
use App\Models\Student;
use Tests\Factories\InstitutionFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(Student::class);

it('computes student course count ', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->state(['course_count' => 10]))
        ->createOne();

    $count = $student->courseCount();

    expect($count)->toBe(20);
});

it('computes student credit unit sum', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->state(['cus' => 24]))
        ->createOne();

    $cus = $student->creditUnitSum();

    expect($cus)->toBe(48);
});

it('computes student grade point sum', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->state(['gps' => 45]))
        ->createOne();

    $gps = $student->gradePointSum();

    expect($gps)->toBe(90);
});

it('computes sum of student cumulative grade point averages', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->sequence(['cgpa' => 3.111], ['cgpa' => 2.712]))
        ->createOne();

    $gpas = $student->cumulativeGradePointAverageSum();

    expect($gpas)->toBe(5.823);
});

it('computes student fcgpa using semester strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->sequence(['cgpa' => 3.111], ['cgpa' => 2.712]))
        ->createOne();

    $cgpa = $student->finalCumulativeGradePointAverage();

    expect($cgpa)->toBe(2.912);
});

it('computes student fcgpa using universal strategy', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::UNIVERSAL->value]);

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->state(['cus' => 26, 'gps' => 90]))
        ->createOne();

    $cgpa = $student->finalCumulativeGradePointAverage();

    expect($cgpa)->toBe(3.462);
});

it('updates student credit unit, grade point sum and averages', function (): void {
    InstitutionFactory::new()->createOne(['strategy' => ComputationStrategy::SEMESTER->value]);

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->sequence(['cgpa' => 3.111], ['cgpa' => 2.712]))
        ->createOne();

    $count = $student->courseCount();
    $cus = $student->creditUnitSum();
    $gps = $student->gradePointSum();
    $cgpas = $student->cumulativeGradePointAverageSum();
    $fcgpa = $student->finalCumulativeGradePointAverage();

    $student->updateCountSumAndAverages();

    assertDatabaseHas(Student::class, [
        'cgpas' => $cgpas * 1000,
        'course_count' => $count,
        'cus' => $cus,
        'fcgpa' => $fcgpa * 1000,
        'gps' => $gps,
        'id' => $student->id,
    ]);
});
