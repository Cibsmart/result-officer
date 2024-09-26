<?php

declare(strict_types=1);

namespace App\Actions\Registrations;

use App\Enums\RawDataStatus;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Enrollment;
use App\Models\Level;
use App\Models\RawRegistration;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;

final class ProcessPortalRegistration
{
    /** @throws \Exception */
    public function execute(RawRegistration $rawRegistration): void
    {
        $student = Student::getUsingRegistrationNumber($rawRegistration->registration_number);
        $session = Session::getUsingName($rawRegistration->session);
        $level = Level::getUsingName($rawRegistration->level);
        $semester = Semester::getUsingName($rawRegistration->semester);
        $course = Course::getUsingOnlineId($rawRegistration->course_id);

        $sessionEnrollment = Enrollment::getOrCreate($student, $session, $level);
        $semesterEnrollment = SemesterEnrollment::getOrCreate($sessionEnrollment, $semester);

        $exists = CourseRegistration::query()
            ->where('semester_enrollment_id', $semesterEnrollment->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            $rawRegistration->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        CourseRegistration::createFromRawRegistration($rawRegistration, $semesterEnrollment, $course);

        $rawRegistration->updateStatus(RawDataStatus::PROCESSED);
    }
}
