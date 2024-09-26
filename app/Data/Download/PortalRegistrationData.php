<?php

declare(strict_types=1);

namespace App\Data\Download;

use App\Enums\RecordSource;
use Spatie\LaravelData\Data;

/** @phpstan-import-type CourseRegistrationDetail from \App\Contracts\CourseRegistrationClient */
final class PortalRegistrationData extends Data
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
        public readonly RecordSource $source,
    ) {
    }

    /** @param CourseRegistrationDetail $data */
    public static function fromArray(array $data): self
    {
        return new self(
            onlineId: (string) $data['id'],
            registrationNumber: $data['registration_number'],
            session: $data['session'],
            semester: $data['semester'],
            level: $data['level'],
            courseId: (string) $data['course_id'],
            creditUnit: (string) $data['credit_unit'],
            registrationDate: PortalDateData::from($data['registration_date']),
            source: RecordSource::PORTAL,
        );
    }
}
