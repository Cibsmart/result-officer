<?php

declare(strict_types=1);

use App\Actions\Vetting\ValidateResults;
use App\Enums\VettingStatus;
use Tests\Factories\VettingEventFactory;
use Tests\Factories\VettingStepFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(ValidateResults::class);

it('validates the integrity of the students results', function (): void {
    $student = createStudentWithResults();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $validation = new ValidateResults();

    $status = $validation->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($validation->report())->toBe('');
});

it('reports students results with tampered score', function (): void {
    $student = createStudentWithResults();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesterEnrollments->first();
    $registration = $semesterEnrollment->registrations->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $result->total_score = 101;
    $result->save();

    $validation = new ValidateResults();

    $status = $validation->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($validation->report())->toBe(
            "{$course->code} in {$semester->name} semester {$session->name} is invalid. \n",
        );

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vetting_step_id' => $vettingStep->id,
    ]);
});

it('reports students results with tampered grade', function (): void {
    $student = createStudentWithResults();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesterEnrollments->first();
    $registration = $semesterEnrollment->registrations->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $result->grade = 'Z';
    $result->save();

    $validation = new ValidateResults();

    $status = $validation->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($validation->report())->toBe(
            "{$course->code} in {$semester->name} semester {$session->name} is invalid. \n",
        );
});

it('reports students results with tampered grade point', function (): void {
    $student = createStudentWithResults();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesterEnrollments->first();
    $registration = $semesterEnrollment->registrations->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $result->grade_point = 150;
    $result->save();

    $validation = new ValidateResults();

    $status = $validation->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($validation->report())->toBe(
            "{$course->code} in {$semester->name} semester {$session->name} is invalid. \n",
        );
});

it('reports all cases of tampered students results', function (): void {
    $student = createStudentWithResults();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesterEnrollments->first();
    $registration = $semesterEnrollment->registrations->first();
    $result = $registration->result;
    $result->grade_point = 150;
    $result->save();

    $registration2 = $semesterEnrollment->registrations->last();
    $result2 = $registration2->result;
    $result2->grade = 'H';
    $result2->save();

    $validation = new ValidateResults();

    $status = $validation->execute($student, $vettingStep);
    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vettable_id' => $result->id,
        'vetting_step_id' => $vettingStep->id,
    ]);

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vettable_id' => $result2->id,
        'vetting_step_id' => $vettingStep->id,
    ]);

    assertDatabaseCount('vetting_reports', 2);
});
