<?php

declare(strict_types=1);

namespace App\Classes;

use App\Data\Download\PortalCourseRegistrationData;
use App\Enums\CourseStatusEnum;
use App\Enums\CreditUnitEnum;
use App\Enums\RecordSource;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\SemesterEnrollment;
use Exception;

final readonly class PendingCourseRegistration
{
    public function __construct(public CourseRegistration $registration)
    {
    }

    public static function new(SemesterEnrollment $semesterEnrollment, PortalCourseRegistrationData $data): self
    {
        $registration = new CourseRegistration();
        $registration->course_id = self::getCourse($data->courseId)->id;
        $registration->credit_unit = CreditUnitEnum::from((int) $data->creditUnit)->value;
        $registration->course_status = CourseStatusEnum::FRESH->value;
        $registration->online_id = $data->onlineId;
        $registration->registration_date = $data->registrationDate->getCarbonDate();
        $registration->semester_enrollment_id = $semesterEnrollment->id;
        $registration->source = RecordSource::PORTAL->value;

        return new self($registration);
    }

    /** @throws \Exception */
    public function save(): bool
    {
        $registrationExists = CourseRegistration::query()
            ->where('course_id', $this->registration->course_id)
            ->where('semester_enrollment_id', $this->registration->semester_enrollment_id)
            ->exists();

        if ($registrationExists) {
            throw new Exception('Course Registration record already exists in the database');
        }

        return $this->registration->save();
    }

    private static function getCourse(string $onlineCourseId): Course
    {
        return Course::query()->where('online_id', $onlineCourseId)->firstOrFail();
    }
}
