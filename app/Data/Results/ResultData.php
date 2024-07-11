<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Course;
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
        public readonly ?string $remark
    ) {
    }

    public static function fromModel(CourseRegistration $courseRegistration): self
    {
        $course = $courseRegistration->course;
        $result = $courseRegistration->result;

        assert($course instanceof Course);
        assert($result instanceof Result);

        return new self(
            id: $courseRegistration->id,
            courseCode: $course->code,
            courseTitle: $course->title,
            creditUnit: $courseRegistration->credit_unit,
            totalScore: $result->total_score,
            grade: $result->grade,
            gradePoint: $result->grade_point,
            remark: $result->remarks,
        );
    }
}
