<?php

declare(strict_types=1);

use App\Actions\Vetting\ValidateResults;
use App\Enums\VettingStatus;
use Tests\Factories\VettingEventFactory;
use Tests\Factories\VettingStepFactory;

it('validates the integrity of the students results', function (): void {
    $student = createStudentWithResults();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $validation = new ValidateResults();

    $status = $validation->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($validation->remarks())->toBe('');
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
        ->and($validation->remarks())->toBe("{$course->code} in {$semester->name} {$session->name} is invalid. \n");
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
        ->and($validation->remarks())->toBe("{$course->code} in {$semester->name} {$session->name} is invalid. \n");
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
        ->and($validation->remarks())->toBe("{$course->code} in {$semester->name} {$session->name} is invalid. \n");
});
