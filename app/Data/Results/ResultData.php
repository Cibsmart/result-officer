<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\CourseRegistration;
use App\Models\Result;
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
        public readonly ?string $remark,
    ) {
    }

    public static function fromModel(CourseRegistration $courseRegistration): self
    {
        $result = $courseRegistration->result
            ? $courseRegistration->result
            : new Result(['grade' => 'F', 'grade_point' => 0, 'remarks' => 'NR', 'total_score' => 0]);

        return new self(
            id: $courseRegistration->id,
            courseCode: $courseRegistration->course->code,
            courseTitle: $courseRegistration->course->title,
            creditUnit: $courseRegistration->credit_unit,
            totalScore: $result->total_score,
            grade: $result->grade,
            gradePoint: $result->grade_point,
            remark: $result->remarks,
        );
    }
}
