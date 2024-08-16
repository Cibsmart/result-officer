<?php

declare(strict_types=1);

namespace App\Data\Download;

use App\Enums\Grade;
use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;
use Spatie\LaravelData\Data;

/** @phpstan-import-type ResultDetail from \App\Contracts\ResultClient */
final class PortalResultData extends Data
{
    public function __construct(
        public readonly string $onlineId,
        public readonly string $courseRegistrationId,
        public readonly RegistrationNumber $registrationNumber,
        public readonly InCourseScore $inCourseScore,
        public readonly ExamScore $examScore,
        public readonly TotalScore $totalScore,
        public readonly Grade $grade,
        public readonly PortalDateData $uploadDate,
    ) {
    }

    /** @param ResultDetail $data */
    public static function fromArray(array $data): self
    {

        $registrationNumber = RegistrationNumber::new($data['registration_number']);

        $inCourseScore = InCourseScore::new((int) $data['in_course']);

        $examScore = ExamScore::new((int) $data['exam_score']);

        $totalScore = TotalScore::fromInCourseAndExam($inCourseScore, $examScore);

        return new self(
            onlineId: $data['id'],
            courseRegistrationId: $data['course_registration_id'],
            registrationNumber: $registrationNumber,
            inCourseScore: $inCourseScore,
            examScore: $examScore,
            totalScore: $totalScore,
            grade: $totalScore->grade($registrationNumber->allowEGrade()),
            uploadDate: PortalDateData::from($data['upload_date']),
        );
    }
}
