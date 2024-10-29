<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

final class ValidateResults
{
    private string $report = '';

    public function execute(Student $student): void
    {
        $sessionEnrollments = $student->sessionEnrollments;

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $semesterEnrollments = $sessionEnrollment->semesters;

            foreach ($semesterEnrollments as $semesterEnrollment) {
                $this->validate($semesterEnrollment, $sessionEnrollment->session);
            }
        }
    }

    public function validate(SemesterEnrollment $semesterEnrollment, Session $session): void
    {
        $courses = $semesterEnrollment->courses()->with('result')->get();

        foreach ($courses as $course) {
            $result = $course->result;

            if (Hash::check($result->getData(), $result->data)) {
                continue;
            }

            $code = $course->course->code;
            $semester = $semesterEnrollment->semester;

            $this->report .= "{$code} in {$semester->name} {$session->name} is invalid. \n";
        }
    }

    public function report(): string
    {
        return $this->report === ''
            ? 'PASSED'
            : $this->report;
    }
}
