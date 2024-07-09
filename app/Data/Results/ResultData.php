<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\CourseRegistration;
use Spatie\LaravelData\Data;

final class ResultData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $courseCode,
        public readonly string $courseTitle,
        public readonly int $creditUnit,
        public readonly int $totalScore,
        public readonly string $grade,
        public readonly int $gradePoint,
    ) {
    }

    public static function fromModel(CourseRegistration $course): self
    {
        return new self(
            $course->id,
            $course->course->code,
            $course->course->title,
            $course->credit_unit,
            $course->result->total_score,
            $course->result->grade,
            $course->result->grade_point,
        );
    }
}
