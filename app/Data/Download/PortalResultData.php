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
        public readonly string $uploadDate,
        public readonly RecordSource $source,
        public readonly string $examDate,
        public readonly string $lecturerName,
        public readonly string $lecturerDepartment,
    ) {
    }

    /** @param ResultDetail $data */
    public static function fromArray(array $data): self
    {
        dd($data);

        return new self(
            onlineId: $data['id'],
            courseRegistrationId: $data['course_registration_id'],
            registrationNumber: $data['registration_number'],
            inCourseScore: $data['in_course'],
            examScore: $data['exam_score'],
            totalScore: $data['total_score'],
            grade: $data['grade'],
            uploadDate: $data['upload_date'],
            source: RecordSource::PORTAL,
            examDate: $data['exam_date'] ?? '',
            lecturerName: $data['lecturer_name'] ?? '',
            lecturerDepartment: $data['lecturer_department'] ?? '',
        );
    }
}
