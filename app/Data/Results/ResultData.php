<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Registration;
use App\Models\Result;
use Spatie\LaravelData\Data;

final class ResultData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $resultId,
        public readonly string $courseCode,
        public readonly string $courseTitle,
        public readonly int $creditUnit,
        public readonly int $totalScore,
        public readonly string $grade,
        public readonly int $gradePoint,
        public readonly ?string $remark,
        public readonly string $dateUpdated,
        public readonly int $inCourseScore,
        public readonly int $inCourseScore2,
        public readonly int $examScore,
    ) {
    }

    public static function fromModel(Registration $courseRegistration): self
    {
        $result = $courseRegistration->result
            ? $courseRegistration->result
            : new Result([
                'grade' => 'F', 'grade_point' => 0, 'remarks' => 'NR',
                'scores' => ['exam' => 0, 'in_course' => 0, 'in_course_2' => 0],
                'total_score' => 0,
            ]);

        $scores = $result->getScores();

        return new self(
            id: $courseRegistration->id,
            resultId: $result->id ? $result->id : 0,
            courseCode: $courseRegistration->course->code,
            courseTitle: $courseRegistration->course->title,
            creditUnit: $courseRegistration->credit_unit->value,
            totalScore: $result->total_score,
            grade: $result->grade,
            gradePoint: $result->grade_point,
            remark: $result->remarks,
            dateUpdated: $result->updated_at ? $result->updated_at->toDateTimeString() : '',
            inCourseScore: (int) $scores['in_course'],
            inCourseScore2: (int) $scores['in_course_2'],
            examScore: (int) $scores['exam'],
        );
    }
}
