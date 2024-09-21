<?php

declare(strict_types=1);

namespace App\Data\Download;

use App\Enums\RecordSource;
use Spatie\LaravelData\Data;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final class PortalResultData extends Data
{
    public function __construct(
        public readonly string $onlineId,
        public readonly string $courseRegistrationId,
        public readonly string $registrationNumber,
        public readonly string $inCourseScore,
        public readonly string $examScore,
        public readonly string $totalScore,
        public readonly string $grade,
        public readonly PortalDateData $uploadDate,
        public readonly RecordSource $source,
        public readonly string $examDate,
        public readonly string $lecturerName,
        public readonly string $lecturerEmail,
        public readonly string $lecturerPhoneNumber,
        public readonly string $lecturerDepartment,
    ) {
    }

    /** @param ResultDetail $data */
    public static function fromArray(array $data): self
    {
        return new self(
            onlineId: (string) $data['id'],
            courseRegistrationId: (string) $data['id'],
            registrationNumber: $data['registration_number'],
            inCourseScore: (string) $data['in_course'],
            examScore: (string) $data['exam_score'],
            totalScore: (string) $data['total_score'],
            grade: $data['grade'],
            uploadDate: PortalDateData::from($data['upload_date']),
            source: RecordSource::PORTAL,
            examDate: $data['exam_date'] ?? '',
            lecturerName: $data['lecturer_name'] ?? '',
            lecturerEmail: $data['lecturer_email'] ?? '',
            lecturerPhoneNumber: $data['lecturer_phone_number'] ?? '',
            lecturerDepartment: $data['lecturer_department'] ?? '',
        );
    }
}
