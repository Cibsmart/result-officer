<?php

declare(strict_types=1);

namespace App\Classes;

use App\Data\Download\PortalCourseRegistrationData;
use App\Enums\CreditUnitEnum;
use App\Enums\RecordSource;
use App\Enums\YearEnum;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Enrollment;
use App\Models\Level;
use App\Models\Semester;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use App\Values\RegistrationNumber;
use Exception;

final readonly class PendingCourseRegistration
{
    public function __construct(public CourseRegistration $registration)
    {
    }

    public static function new(PortalCourseRegistrationData $data): self
    {
        $studentId = self::getStudentId(RegistrationNumber::new($data->registrationNumber)->value);
        $sessionId = self::getSessionId($data->session);
        $level_id = self::getLevelId($data->level);

        $sessionEnrollment = Enrollment::firstOrNew(
            ['student_id' => $studentId, 'session_id' => $sessionId, 'level_id' => $level_id],
            ['year_id' => YearEnum::FIRST->value],
        );

        $semesterId = self::getSemesterId($data->semester);

        $semesterEnrollment = SemesterEnrollment::firstOrNew(
            ['enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semesterId],
        );

        $registration = new CourseRegistration([
            'course_id' => self::getCourseId($data->courseId),
            'credit_unit' => CreditUnitEnum::from((int) $data->creditUnit)->value,
            'online_id' => $data->onlineId,
            'registration_date' => $data->registrationDate->getCarbonDate(),
            'semester_enrollment_id' => $semesterEnrollment->id,
            'source' => RecordSource::PORTAL,
        ]);

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

    private static function getStudentId(string $registration): int
    {
        return Student::query()->where('registration_number', $registration)->firstOrFail()->id;
    }

    private static function getSessionId(string $session): int
    {
        return Session::query()->where('name', $session)->firstOrFail()->id;
    }

    private static function getLevelId(string $level): int
    {
        return Level::query()->where('name', $level)->firstOrFail()->id;
    }

    private static function getSemesterId(string $semester): int
    {
        return Semester::query()->where('name', $semester)->firstOrFail()->id;
    }

    private static function getCourseId(string $onlineCourseId): int
    {
        return Course::query()->where('online_id', $onlineCourseId)->firstOrFail()->id;
    }
}
