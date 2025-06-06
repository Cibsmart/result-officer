<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Data\Query\StudentCoursesData;
use App\Enums\Grade;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\Registration;
use App\Models\Student;
use App\Queries\StudentCourses;
use Illuminate\Support\Collection;

final class VerifyFailedCourses extends ReportVettingStep
{
    public function execute(Student $student): VettingStatus
    {
        $this->createVettingStep($student, VettingType::CHECK_FAILED_COURSES);

        $courses = StudentCourses::for($student)->get();

        if ($courses->isEmpty()) {
            return VettingStatus::UNCHECKED;
        }

        $failedCourses = $this->getFailedCourses($courses);
        $sortedFailedCourses = $failedCourses->sortBy(['session', 'semester', 'course_code'])->values();

        $passed = true;

        foreach ($sortedFailedCourses as $failedCourse) {
            $message = "{$failedCourse->courseCode} - {$failedCourse->session} {$failedCourse->semester} semester";

            $registration = Registration::query()->find($failedCourse->registrationId);

            $this->report($registration, $message);

            $passed = false;
        }

        return $passed
            ? VettingStatus::PASSED
            : VettingStatus::FAILED;
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData> $courses
     * @return \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData>
     */
    private function getFailedCourses(Collection $courses): Collection
    {
        $failedCourses = [];

        foreach ($courses as $course) {
            if (! in_array($course->grade, Grade::passGrade(), true)) {
                $failedCourses[$course->courseId] = $course;

                continue;
            }

            if (! $this->passedFailedCourse($course, $failedCourses)) {
                continue;
            }

            unset($failedCourses[$course->courseId]);
        }

        return collect($failedCourses);
    }

    /** @param array<int, \App\Data\Query\StudentCoursesData> $failedCourses */
    private function passedFailedCourse(StudentCoursesData $course, array $failedCourses): bool
    {
        return array_key_exists($course->courseId, $failedCourses)
            && ($course->sessionId > $failedCourses[$course->courseId]->sessionId);
    }
}
