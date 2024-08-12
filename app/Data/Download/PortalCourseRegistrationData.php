<?php

declare(strict_types=1);

namespace App\Data\Download;

use Spatie\LaravelData\Data;

/**
 * @phpstan-type CourseRegistrationType array{id:string, registration_number:string, session:string,semester:string,
 *     level:string, course_id:string, credit_unit:string, registration_date:array{day:string,month:string,
 *     year:string}
 */
final class PortalCourseRegistrationData extends Data
{
    public function __construct(
        public readonly string $onlineId,
        public readonly string $registrationNumber,
        public readonly string $session,
        public readonly string $semester,
        public readonly string $level,
        public readonly string $courseId,
        public readonly string $creditUnit,
        public readonly PortalDateData $registrationDate,
    ) {
    }

    /** @param CourseRegistrationType $data */
    public static function fromArray(array $data): self
    {
        return new self(
            onlineId: (string) $data['id'],
            registrationNumber: $data['registration_number'],
            session: $data['session'],
            semester: $data['semester'],
            level: $data['level'],
            courseId: $data['course_id'],
            creditUnit: (string) $data['credit_unit'],
            registrationDate: PortalDateData::from($data['registration_date']),
        );
    }
}
