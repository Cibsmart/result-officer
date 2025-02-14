<?php

declare(strict_types=1);

namespace App\Data\FinalResults;

use App\Models\FinalResult;
use Spatie\LaravelData\Data;

final class FinalResultData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $courseCode,
        public readonly string $courseTitle,
        public readonly int $creditUnit,
        public readonly int $totalScore,
        public readonly string $grade,
        public readonly int $gradePoint,
        public readonly ?string $remark,
        public readonly string $dateUpdated,
    ) {
    }

    public static function fromModel(FinalResult $finalResult): self
    {
        return new self(
            id: $finalResult->id,
            courseCode: $finalResult->finalCourse->code,
            courseTitle: $finalResult->finalCourse->title,
            creditUnit: $finalResult->credit_unit->value,
            totalScore: $finalResult->total_score,
            grade: $finalResult->grade,
            gradePoint: $finalResult->grade_point,
            remark: $finalResult->remarks,
            dateUpdated: $finalResult->updated_at ? $finalResult->updated_at->toDateTimeString() : '',
        );
    }
}
