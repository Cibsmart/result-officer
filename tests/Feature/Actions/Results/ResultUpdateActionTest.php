<?php

declare(strict_types=1);

use App\Actions\Results\ResultUpdateAction;
use App\Enums\CreditUnit;
use App\Enums\Grade;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\Registration;
use App\Models\StudentHistory;

use function Pest\Laravel\assertDatabaseHas;

it('can update result credit unit', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();

    $changedValue = ['credit_unit' => CreditUnit::FIVE->value];

    (new ResultUpdateAction())->execute($student, $registration, $changedValue);

    $registration->fresh();

    expect($registration->credit_unit->value)->toBe(CreditUnit::FIVE->value);
});

it('can update result in course score', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();
    $result = $registration->result;
    $newValue = 30;

    $changedValue = ['in_course' => $newValue];

    (new ResultUpdateAction())->execute($student, $registration, $changedValue);

    $result->fresh();
    $scores = $result->scores;

    expect($scores['in_course'])->toBe($newValue);
});

it('can update result exam score', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();
    $result = $registration->result;
    $newValue = 60;

    $changedValue = ['exam' => $newValue];

    (new ResultUpdateAction())->execute($student, $registration, $changedValue);

    $result->fresh();
    $scores = $result->scores;

    expect($scores['exam'])->toBe($newValue);
});

it('records the update in student history table', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();
    $result = $registration->result;

    $newValue = CreditUnit::FIVE->value;

    $changedValue = ['credit_unit' => $newValue];

    $scores = json_decode($result->scores);
    $oldResultDetails = "{$registration->credit_unit->value}-{$scores->in_course}-{$scores->exam}";

    (new ResultUpdateAction())->execute($student, $registration, $changedValue);

    $data = [
        'new' => "{$newValue}-{$scores->in_course}-{$scores->exam}",
        'old' => $oldResultDetails,
    ];

    assertDatabaseHas(StudentHistory::class, [
        'data' => json_encode($data),
        'field' => StudentModifiableField::RESULT,
        'modifiable_id' => $registration->id,
        'modifiable_type' => (new Registration())->getMorphClass(),
        'student_id' => $student->id,
    ]);
});

it('recomputes total, grade, and grade point after update', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();
    $result = $registration->result;

    $changedValue = ['credit_unit' => 3, 'in_course' => 30, 'exam' => 70];

    (new ResultUpdateAction())->execute($student, $registration, $changedValue);

    $result->fresh();

    expect($result->total_score)->toBe(100)
        ->and($result->grade)->toBe(Grade::A->value)
        ->and($result->grade_point)->toBe(15);
});

it('updates result details table after update', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();
    $result = $registration->result;

    $changedValue = ['credit_unit' => 3, 'in_course' => 30, 'exam' => 70];

    (new ResultUpdateAction())->execute($student, $registration, $changedValue);

    $result->fresh();
    $detail = $result->resultDetail;

    $text = "{$registration->id}-100-A-15";

    expect($detail->value)->toBe($text);
});
