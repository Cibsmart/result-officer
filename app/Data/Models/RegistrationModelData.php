<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Enums\CourseStatus;
use App\Enums\CreditUnit;
use App\Enums\RecordSource;
use App\Models\Course;
use App\Models\LegacyFinalResult;
use App\Models\LegacyResult;
use App\Models\RawRegistration;
use App\Models\Registration;
use App\Models\SemesterEnrollment;
use App\Values\DateValue;

final readonly class RegistrationModelData
{
    public function __construct(
        public SemesterEnrollment $semesterEnrollment,
        public Course $course,
        public CreditUnit $creditUnit,
        public CourseStatus $courseStatus,
        public ?DateValue $registrationDate,
        public ?string $onlineId,
        public RecordSource $source,
    ) {
    }

    public static function fromRawRegistration(
        RawRegistration $registration,
        SemesterEnrollment $semesterEnrollment,
        Course $course,
    ): self {
        $registrationDate = DateValue::fromValue($registration->registration_date);

        return new self(
            semesterEnrollment: $semesterEnrollment,
            course: $course,
            creditUnit: CreditUnit::from($registration->credit_unit),
            courseStatus: CourseStatus::FRESH,
            registrationDate: $registrationDate,
            onlineId: $registration->online_id,
            source: RecordSource::PORTAL,
        );
    }

    public static function fromLegacyResult(
        SemesterEnrollment $semesterEnrollment,
        LegacyFinalResult|LegacyResult $result,
        CourseStatus $courseStatus,
    ): self {
        return new self(
            semesterEnrollment: $semesterEnrollment,
            course: Course::getUsingLegacyCourseId($result->legacy_course_id),
            creditUnit: CreditUnit::from($result->credit_unit),
            courseStatus: $courseStatus,
            registrationDate: null,
            onlineId: null,
            source: RecordSource::LEGACY,
        );
    }

    public function getModel(): Registration
    {

        $registration = new Registration();

        $registration->course_id = $this->course->id;
        $registration->credit_unit = $this->creditUnit->value;
        $registration->course_status = $this->courseStatus->value;
        $registration->online_id = $this->onlineId;
        $registration->registration_date = $this->registrationDate->value;
        $registration->semester_enrollment_id = $this->semesterEnrollment->id;
        $registration->source = RecordSource::PORTAL;

        return $registration;
    }
}
