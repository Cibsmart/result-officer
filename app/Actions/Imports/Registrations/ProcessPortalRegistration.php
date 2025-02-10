<?php

declare(strict_types=1);

namespace App\Actions\Import\Registrations;

use App\Enums\RawDataStatus;
use App\Models\Course;
use App\Models\Level;
use App\Models\RawCourseAlternative;
use App\Models\RawRegistration;
use App\Models\Registration;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\SessionEnrollment;
use App\Models\Student;

final class ProcessPortalRegistration
{
    /** @throws \Exception */
    public function execute(RawRegistration $rawRegistration): void
    {
        $alternativeOnlineCourseId = RawCourseAlternative::getAlternativeOnlineCourseId($rawRegistration->course_id);

        $onlineCourseId = $alternativeOnlineCourseId === null
            ? $rawRegistration->course_id
            : $alternativeOnlineCourseId->alternative_course_id;

        $student = Student::getUsingRegistrationNumber($rawRegistration->getRegistrationNumber());
        $session = Session::getUsingName($rawRegistration->session);
        $level = Level::getUsingName($rawRegistration->level);
        $semester = Semester::getUsingName($rawRegistration->semester);
        $course = Course::getUsingOnlineId((string) $onlineCourseId);

        $sessionEnrollment = SessionEnrollment::getOrCreate($student, $session, $level);
        $student->updateStatus($student->getStatus());
        $semesterEnrollment = SemesterEnrollment::getOrCreate($sessionEnrollment, $semester);

        $duplicate = $this->checkForDuplicate($semesterEnrollment, $course);

        if ($duplicate) {
            $rawRegistration->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        $registration = Registration::createFromRawRegistration($rawRegistration, $semesterEnrollment, $course);

        $rawRegistration->updateStatusAndRegistration(RawDataStatus::PROCESSED, $registration);
    }

    private function checkForDuplicate(SemesterEnrollment $semesterEnrollment, Course $course): bool
    {
        $courses = Course::query()
            ->whereIn('id', $semesterEnrollment->registrations()->pluck('course_id'))
            ->get();

        return $courses->contains('id', $course->id) || $courses->contains('code', $course->code);
    }
}
