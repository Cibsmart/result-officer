<?php

declare(strict_types=1);

namespace App\Data\Results;

use App\Models\Enrollment;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SessionResultData extends Data
{
    public function __construct(
        public readonly int $id,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Results\SemesterResultData> */
        public readonly Collection $semesterResults,
        public readonly string $session,
        public readonly float $cummulativeGradePointAverage,
    ) {
    }

    public static function fromModel(Enrollment $enrollment): self
    {
        $semesters = SemesterResultData::collect($enrollment->semesters);

        $cumulativeGradePointAverage = round(
            $semesters->sum('gradePointAverage') / $semesters->count(),
            3,
        );

        return new self($enrollment->id, $semesters, $enrollment->session->name, $cumulativeGradePointAverage);
    }
}
