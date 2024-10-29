<?php

declare(strict_types=1);

use App\Actions\Vetting\ValidateResults;

it('validates the integrity of the students results', function (): void {
    $student = createStudentWithResults();

    $validation = new ValidateResults();

    $validation->execute($student);

    expect($validation->report())->toBe('PASSED');
});

it('reports students results with tampered score', function (): void {
    $student = createStudentWithResults();

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesters->first();
    $registration = $semesterEnrollment->courses->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $result->total_score = 101;
    $result->save();

    $validation = new ValidateResults();

    $validation->execute($student);

    expect($validation->report())->toBe("{$course->code} in {$semester->name} {$session->name} is invalid. \n");
});

it('reports students results with tampered grade', function (): void {
    $student = createStudentWithResults();

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesters->first();
    $registration = $semesterEnrollment->courses->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $result->grade = 'Z';
    $result->save();

    $validation = new ValidateResults();

    $validation->execute($student);

    expect($validation->report())->toBe("{$course->code} in {$semester->name} {$session->name} is invalid. \n");
});

it('reports students results with tampered grade point', function (): void {
    $student = createStudentWithResults();

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesters->first();
    $registration = $semesterEnrollment->courses->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $result->grade_point = 150;
    $result->save();

    $validation = new ValidateResults();

    $validation->execute($student);

    expect($validation->report())->toBe("{$course->code} in {$semester->name} {$session->name} is invalid. \n");
});
