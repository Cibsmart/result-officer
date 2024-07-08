<?php

namespace App\Data\Results;

use App\Models\Result;
use Spatie\LaravelData\Data;

final class ResultData extends Data
{
    public function __construct(
        public readonly string $courseCode,
        public readonly string $courseTitle,
        public readonly int $creditUnit,
        public readonly int $totalScore,
        public readonly string $grade,
        public readonly int $gradePoint,
    ) {
    }

    public static function fromModel(Result $result): self
    {
        return new self(
            $result->course->code,
            $result->course->title,
            $result->creditUnit->value,
            $result->total_score,
            $result->grade,
            $result->grade_point
        );
    }
}
